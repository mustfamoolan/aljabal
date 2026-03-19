@extends('layouts.vertical', ['title' => 'المحادثات المباشرة'])

@section('css')
<style>
    .chat-wrapper {
        display: flex;
        height: calc(100vh - 180px);
        background: #fff;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
    }
    .chat-sidebar {
        width: 320px;
        border-left: 1px solid #eee;
        display: flex;
        flex-direction: column;
    }
    .chat-main {
        flex: 1;
        display: flex;
        flex-direction: column;
        background: #f8f9fa;
    }
    .chat-list {
        flex: 1;
        overflow-y: auto;
    }
    .chat-item {
        padding: 15px;
        border-bottom: 1px solid #f5f5f5;
        cursor: pointer;
        transition: 0.2s;
        display: flex;
        align-items: center;
    }
    .chat-item:hover {
        background: #f0f7ff;
    }
    .chat-item.active {
        background: #e6f2ff;
        border-right: 4px solid #007bff;
    }
    .chat-avatar {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        margin-left: 12px;
        object-fit: cover;
    }
    .chat-info {
        flex: 1;
    }
    .chat-name {
        font-weight: 600;
        font-size: 14px;
        margin-bottom: 3px;
    }
    .chat-last-msg {
        font-size: 12px;
        color: #888;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 180px;
    }
    .chat-header {
        padding: 15px 25px;
        background: #fff;
        border-bottom: 1px solid #eee;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .messages-container {
        flex: 1;
        padding: 25px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
    }
    .message {
        margin-bottom: 15px;
        max-width: 75%;
        padding: 12px 18px;
        border-radius: 15px;
        font-size: 14px;
        line-height: 1.5;
        position: relative;
    }
    .message.received {
        align-self: flex-start;
        background: #fff;
        border-bottom-right-radius: 2px;
        color: #333;
        box-shadow: 0 2px 5px rgba(0,0,0,0.02);
    }
    .message.sent {
        align-self: flex-end;
        background: #007bff;
        color: #fff;
        border-bottom-left-radius: 2px;
    }
    .message-time {
        font-size: 10px;
        margin-top: 5px;
        opacity: 0.7;
        display: block;
        text-align: left;
    }
    .chat-input-area {
        padding: 20px;
        background: #fff;
        border-top: 1px solid #eee;
    }
    .unread-badge {
        background: #ff4757;
        color: #fff;
        font-size: 10px;
        padding: 2px 7px;
        border-radius: 10px;
    }
    .search-box {
        padding: 15px;
        background: #fcfcfc;
        border-bottom: 1px solid #eee;
    }
    #message-form {
        display: flex;
        gap: 10px;
    }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="chat-wrapper" id="chat-app">
            <!-- Sidebar -->
            <div class="chat-sidebar">
                <div class="p-3 border-bottom">
                    <ul class="nav nav-pills nav-justified" id="chatTabs" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active" id="reps-tab" data-bs-toggle="pill" data-bs-target="#reps-content" type="button">المندوبين</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" id="chats-tab" data-bs-toggle="pill" data-bs-target="#chats-content" type="button">المحادثات</button>
                        </li>
                    </ul>
                </div>

                <div class="tab-content h-100 overflow-hidden">
                    <!-- Representatives List -->
                    <div class="tab-pane fade show active h-100" id="reps-content">
                        <div class="p-2 border-bottom">
                            <input type="text" class="form-control form-control-sm" placeholder="بحث عن مندوب..." id="search-reps">
                        </div>
                        <div class="chat-list" id="reps-list">
                            <div class="text-center p-5">
                                <div class="spinner-border spinner-border-sm text-primary"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Conversations -->
                    <div class="tab-pane fade h-100" id="chats-content">
                        <div class="chat-list" id="conversations-list">
                            <div class="text-center p-5 text-muted small">لا يوجد محادثات نشطة</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Chat -->
            <!-- ... same chat-main ... -->
        </div>
    </div>
</div>
@endsection

@section('script')
@vite(['resources/js/pages/chat.js'])
@endsection
