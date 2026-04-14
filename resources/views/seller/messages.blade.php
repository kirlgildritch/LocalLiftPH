@extends('layouts.seller')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/messages.css') }}">

<section class="dashboard-wrapper">
    <div class="container">
        <div class="dashboard-layout">

            @include('seller.partials.sidebar')

            <main class="dashboard-main">
                <div class="messages-header">
                    <h2>Messages</h2>
                    <button class="new-message-btn">New Message</button>
                </div>

                <div class="divider"></div>

                <div class="messages-layout">
                    <!-- LEFT: CHAT LIST -->
                    <div class="chat-list-panel">
                        <div class="chat-item active">
                            <img src="https://i.pravatar.cc/50?img=5" alt="Anna Santos">
                            <div class="chat-info">
                                <h4>Anna Santos</h4>
                                <p>Is the pink dress still available?</p>
                                <span>10:30 AM</span>
                            </div>
                            <div class="chat-badge">2</div>
                        </div>

                        <div class="chat-item">
                            <img src="https://i.pravatar.cc/50?img=12" alt="Mark Reyes">
                            <div class="chat-info">
                                <h4>Mark Reyes</h4>
                                <p>Can you ship to Cebu?</p>
                                <span>Yesterday</span>
                            </div>
                            <div class="chat-badge red">1</div>
                        </div>

                        <div class="chat-item">
                            <img src="https://i.pravatar.cc/50?img=32" alt="Liza Delacruz">
                            <div class="chat-info">
                                <h4>Liza Delacruz</h4>
                                <p>Thanks for the quick delivery!</p>
                                <span>2 days ago</span>
                            </div>
                            <div class="chat-badge gray">0</div>
                        </div>

                        <div class="chat-item">
                            <img src="https://i.pravatar.cc/50?img=22" alt="John Villanueva">
                            <div class="chat-info">
                                <h4>John Villanueva</h4>
                                <p>Do you have size 10?</p>
                                <span>1 week ago</span>
                            </div>
                            <div class="chat-badge gray">0</div>
                        </div>
                    </div>

                    <!-- RIGHT: CHAT WINDOW -->
                    <div class="chat-window">
                        <div class="chat-window-header">
                            <h3>Chat with Anna Santos</h3>
                            <button class="close-btn">&times;</button>
                        </div>

                        <div class="chat-body">
                            <div class="message-row left">
                                <img src="https://i.pravatar.cc/45?img=5" alt="Anna Santos">
                                <div class="message-bubble">
                                    <strong>Anna Santos</strong>
                                    <p>Hi! I saw the pink floral dress in your shop. Is it still available in size M?</p>
                                </div>
                                <span class="message-time">10:30 AM</span>
                            </div>

                            <div class="message-row left">
                                <img src="https://i.pravatar.cc/45?img=5" alt="Anna Santos">
                                <div class="message-bubble">
                                    <strong>Anna Santos</strong>
                                    <p>Yes, it is! We have size M in stock. Would you like to place an order?</p>
                                </div>
                                <span class="message-time">10:32 AM</span>
                            </div>

                            <div class="message-row left">
                                <img src="https://i.pravatar.cc/45?img=5" alt="Anna Santos">
                                <div class="message-bubble">
                                    <strong>Anna Santos</strong>
                                    <p>Great! Can you hold it for me? I’ll order it now. Is there a discount code?</p>
                                </div>
                                <span class="message-time">10:35 AM</span>
                            </div>

                            <div class="message-row left">
                                <img src="https://i.pravatar.cc/45?img=5" alt="Anna Santos">
                                <div class="message-bubble">
                                    <strong>Anna Santos</strong>
                                    <p>Sure! Use code PINK10 for 10% off. I’ll hold it for 24 hours.</p>
                                </div>
                                <span class="message-time">10:36 AM</span>
                            </div>
                        </div>

                        <div class="chat-input-area">
                            <input type="text" placeholder="Type your message here...">
                            <button class="attach-btn">
                                <i class="fa-solid fa-paperclip"></i>
                            </button>
                            <button class="send-btn">Send</button>
                        </div>
                    </div>
                </div>
            </main>

        </div>
    </div>
</section>
@endsection