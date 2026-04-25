<?php

return [
    'name' => 'LocalLift HelpBot',
    'intro' => '',
    'empty_state' => 'Ask a simple question or tap a topic below.',
    'fallback' => 'Try asking about orders, checkout, shipping, payments, tracking, or messaging a seller.',
    'quick_questions' => [
        'order',
        'checkout',
        'shipping',
        'message_seller',
        'order_tracking',
        'payment',
    ],
    'faqs' => [
        'order' => [
            'question' => 'How do I place an order?',
            'keywords' => ['order', 'buy', 'place order', 'purchase', 'how to order'],
            'answer' => 'Open a product page, choose the quantity you want, add it to your cart, then continue to checkout to place the order.',
        ],
        'checkout' => [
            'question' => 'How does checkout work?',
            'keywords' => ['checkout', 'check out', 'pay now', 'place order', 'confirm order'],
            'answer' => 'Review the items in your cart, confirm your shipping details, choose your payment method, and submit your order from the checkout page.',
        ],
        'cart' => [
            'question' => 'How do I manage my cart?',
            'keywords' => ['cart', 'basket', 'remove item', 'update quantity', 'shopping cart'],
            'answer' => 'Use the cart page to review items, change quantities, remove products, and then proceed to checkout when you are ready.',
        ],
        'shipping' => [
            'question' => 'What should I know about shipping?',
            'keywords' => ['shipping', 'delivery', 'ship', 'courier', 'arrival'],
            'answer' => 'Shipping details depend on the seller and your selected delivery option. Check the product page and your order summary for the delivery information shown before checkout.',
        ],
        'seller_registration' => [
            'question' => 'How can I register as a seller?',
            'keywords' => ['seller registration', 'register seller', 'become a seller', 'seller account', 'sell on locallift'],
            'answer' => 'Use the seller sign-up option from the site and complete the required account and shop information to start listing products as a seller.',
        ],
        'message_seller' => [
            'question' => 'How do I message a seller?',
            'keywords' => ['message seller', 'chat seller', 'contact seller', 'send message', 'talk to seller'],
            'answer' => 'Open the seller or product page and use the message option there. Your existing buyer-seller chat will handle the conversation.',
        ],
        'order_tracking' => [
            'question' => 'How do I track my order?',
            'keywords' => ['order tracking', 'track order', 'where is my order', 'tracking', 'order status'],
            'answer' => 'Go to your orders page and open the order details to check the latest status and tracking updates available for that order.',
        ],
        'payment' => [
            'question' => 'What payment options are available?',
            'keywords' => ['payment', 'pay', 'payment method', 'gcash', 'cod', 'cash on delivery'],
            'answer' => 'Available payment methods are shown during checkout. Choose the option listed there that works best for your order before submitting it.',
        ],
    ],
];
