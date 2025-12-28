/**
 * Topbar Notifications Handler
 * Manages notifications dropdown and badge count
 */

class TopbarNotifications {
    constructor() {
        this.badgeElement = document.getElementById('notifications-badge-count');
        this.listElement = document.getElementById('notifications-list');
        this.dropdownElement = document.getElementById('page-header-notifications-dropdown');
        this.markAllReadBtn = document.getElementById('mark-all-read-btn');
        this.loaded = false;
        this.pollingInterval = null;
        this.lastCount = 0; // Track last notification count

        this.init();
    }

    init() {
        // Load unread count on page load (don't play sound on initial load)
        this.updateBadgeCount().then(() => {
            // After initial load, set lastCount to current count to avoid playing sound
            if (this.badgeElement) {
                this.lastCount = parseInt(this.badgeElement.textContent) || 0;
            }
        });

        // Load notifications when dropdown is opened
        if (this.dropdownElement) {
            this.dropdownElement.addEventListener('shown.bs.dropdown', () => {
                if (!this.loaded) {
                    this.loadNotifications();
                }
            });
        }

        // Mark all as read
        if (this.markAllReadBtn) {
            this.markAllReadBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.markAllAsRead();
            });
        }

        // Start polling for unread count (every 30 seconds)
        this.startPolling();
    }

    /**
     * Update badge count
     */
    async updateBadgeCount() {
        try {
            const response = await fetch('/api/admin/notifications/unread-count', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
            });

            if (response.ok) {
                const data = await response.json();
                if (this.badgeElement) {
                    const count = data.count || 0;
                    const previousCount = this.lastCount;
                    this.lastCount = count;

                    this.badgeElement.textContent = count;
                    if (count === 0) {
                        this.badgeElement.style.display = 'none';
                    } else {
                        this.badgeElement.style.display = 'inline-block';

                        // Play sound if count increased (new notification)
                        if (count > previousCount && previousCount > 0) {
                            this.playNotificationSound();
                        }
                    }
                }
            }
        } catch (error) {
            console.error('Error updating badge count:', error);
        }
    }

    /**
     * Play notification sound
     */
    playNotificationSound() {
        try {
            // Try to play audio file first (if available)
            const audio = new Audio('/sounds/notification.mp3');
            audio.volume = 0.5; // Set volume to 50%

            audio.play().catch(error => {
                console.log('[TopbarNotifications] Audio file not found, using Web Audio API fallback:', error);
                // Fallback to Web Audio API if file doesn't exist
                this.playWebAudioNotification();
            });
        } catch (error) {
            console.log('[TopbarNotifications] Using Web Audio API fallback:', error);
            // Fallback to Web Audio API
            this.playWebAudioNotification();
        }
    }

    /**
     * Generate notification sound using Web Audio API
     */
    playWebAudioNotification() {
        try {
            const audioContext = new (window.AudioContext || window.webkitAudioContext)();
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();

            // Configure sound: pleasant two-tone notification
            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);

            // First tone (higher pitch)
            oscillator.frequency.setValueAtTime(800, audioContext.currentTime);
            oscillator.type = 'sine';
            gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.1);

            oscillator.start(audioContext.currentTime);
            oscillator.stop(audioContext.currentTime + 0.1);

            // Second tone (lower pitch) after a short delay
            setTimeout(() => {
                const oscillator2 = audioContext.createOscillator();
                const gainNode2 = audioContext.createGain();

                oscillator2.connect(gainNode2);
                gainNode2.connect(audioContext.destination);

                oscillator2.frequency.setValueAtTime(600, audioContext.currentTime);
                oscillator2.type = 'sine';
                gainNode2.gain.setValueAtTime(0.3, audioContext.currentTime);
                gainNode2.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.15);

                oscillator2.start(audioContext.currentTime);
                oscillator2.stop(audioContext.currentTime + 0.15);
            }, 100);
        } catch (error) {
            console.warn('[TopbarNotifications] Could not play notification sound:', error);
        }
    }

    /**
     * Load notifications list
     */
    async loadNotifications() {
        if (!this.listElement) return;

        this.listElement.innerHTML = '<div class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>';

        try {
            const response = await fetch('/notifications?filter=unread&per_page=5', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
            });

            if (response.ok) {
                const data = await response.json();
                this.renderNotifications(data.data || []);
                this.loaded = true;
            } else {
                this.listElement.innerHTML = '<div class="text-center py-4 text-muted">لا توجد إشعارات</div>';
            }
        } catch (error) {
            console.error('Error loading notifications:', error);
            this.listElement.innerHTML = '<div class="text-center py-4 text-danger">حدث خطأ في تحميل الإشعارات</div>';
        }
    }

    /**
     * Render notifications list
     */
    renderNotifications(notifications) {
        if (!notifications || notifications.length === 0) {
            this.listElement.innerHTML = '<div class="text-center py-4 text-muted">لا توجد إشعارات غير مقروءة</div>';
            return;
        }

        let html = '';
        notifications.forEach(notification => {
            const isUnread = !notification.read_at;
            const timeAgo = this.getTimeAgo(notification.created_at);
            const icon = notification.type === 'low_stock' ? 'solar:box-bold-duotone' : 'solar:bell-bing-bold-duotone';
            const bgClass = isUnread ? 'bg-light' : '';

            html += `
                <a href="${notification.data?.url || `/notifications/${notification.id}`}"
                   class="dropdown-item py-3 border-bottom ${bgClass}"
                   onclick="topbarNotifications.markAsRead(${notification.id}, event)">
                    <div class="d-flex">
                        <div class="flex-shrink-0">
                            <iconify-icon icon="${icon}" class="fs-20 text-primary"></iconify-icon>
                        </div>
                        <div class="flex-grow-1 ms-2">
                            <p class="mb-0 fw-semibold">${this.escapeHtml(notification.title)}</p>
                            <p class="mb-0 text-muted small">${this.escapeHtml(notification.body)}</p>
                            <small class="text-muted">${timeAgo}</small>
                        </div>
                    </div>
                </a>
            `;
        });

        this.listElement.innerHTML = html;
    }

    /**
     * Mark notification as read
     */
    async markAsRead(notificationId, event) {
        if (event) {
            event.preventDefault();
        }

        try {
            const response = await fetch(`/notifications/${notificationId}/read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                },
                credentials: 'same-origin',
            });

            if (response.ok) {
                // Update badge count
                this.updateBadgeCount();
                // Reload notifications
                this.loaded = false;
                this.loadNotifications();
            }
        } catch (error) {
            console.error('Error marking notification as read:', error);
        }
    }

    /**
     * Mark all notifications as read
     */
    async markAllAsRead() {
        try {
            const response = await fetch('/notifications/read-all', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                },
                credentials: 'same-origin',
            });

            if (response.ok) {
                // Update badge count
                this.updateBadgeCount();
                // Reload notifications
                this.loaded = false;
                this.loadNotifications();
            }
        } catch (error) {
            console.error('Error marking all as read:', error);
        }
    }

    /**
     * Start polling for unread count
     */
    startPolling() {
        // Poll every 30 seconds
        this.pollingInterval = setInterval(() => {
            this.updateBadgeCount();
        }, 30000);
    }

    /**
     * Stop polling
     */
    stopPolling() {
        if (this.pollingInterval) {
            clearInterval(this.pollingInterval);
            this.pollingInterval = null;
        }
    }

    /**
     * Get time ago string
     */
    getTimeAgo(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diff = now - date;
        const seconds = Math.floor(diff / 1000);
        const minutes = Math.floor(seconds / 60);
        const hours = Math.floor(minutes / 60);
        const days = Math.floor(hours / 24);

        if (days > 0) return `منذ ${days} يوم`;
        if (hours > 0) return `منذ ${hours} ساعة`;
        if (minutes > 0) return `منذ ${minutes} دقيقة`;
        return 'الآن';
    }

    /**
     * Escape HTML
     */
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// Initialize when DOM is ready
let topbarNotifications;
document.addEventListener('DOMContentLoaded', () => {
    topbarNotifications = new TopbarNotifications();
    // Make it globally available for notifications.js
    window.topbarNotifications = topbarNotifications;
});

export default TopbarNotifications;
