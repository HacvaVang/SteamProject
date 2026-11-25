<?php
/* Sample data. Replace with an FAQ model (MySQL) later.
   Each body block is one of: h (heading), p (paragraph), ul / ol (lists). */
return [
    'categories' => ['Account', 'Purchases & Refunds', 'Steam Client', 'Community'],

    'faqs' => [
        1 => [
            'title'   => 'How to request a refund',
            'cat'     => 'Purchases & Refunds',
            'updated' => '2026-10-12',
            'body'    => [
                ['p'  => 'You can request a refund for almost any game or in-game purchase, for any reason, within the refund window.'],
                ['h'  => 'What can I request a refund for?'],
                ['ul' => [
                    'Games purchased within the last 14 days',
                    'Games with less than 2 hours of playtime',
                    'Add-on content for a game you own, under the same limits',
                ]],
                ['h'  => 'How do I request a refund?'],
                ['ol' => [
                    'Sign in and open the Purchases page of your account.',
                    'Select the purchase you want refunded.',
                    'Choose "I would like a refund" and pick a reason.',
                    'Select the refund method and submit the request.',
                ]],
                ['p'  => 'Refund requests are normally reviewed within a few days.'],
            ],
        ],
        2 => [
            'title'   => 'I forgot my account name or password',
            'cat'     => 'Account',
            'updated' => '2026-09-30',
            'body'    => [
                ['p'  => 'If you cannot sign in, you can recover your account using the email address or phone number linked to it.'],
                ['ol' => [
                    'Open the sign-in page and choose "Forgot Password?".',
                    'Enter your account name, email address or phone number.',
                    'Follow the instructions in the message we send you.',
                    'Choose a new password that you do not use anywhere else.',
                ]],
            ],
        ],
        3 => [
            'title'   => 'How do I change my account email address?',
            'cat'     => 'Account',
            'updated' => '2026-08-21',
            'body'    => [
                ['p'  => 'You can change the email address from your account details page.'],
                ['ol' => [
                    'Open Account Details.',
                    'Select "Change contact email address".',
                    'Confirm the change using the code sent to your current address.',
                ]],
            ],
        ],
        4 => [
            'title'   => 'Why is my game download slow?',
            'cat'     => 'Steam Client',
            'updated' => '2026-10-02',
            'body'    => [
                ['p'  => 'Slow downloads are usually caused by the network, not by the game servers.'],
                ['h'  => 'Things to try'],
                ['ul' => [
                    'Pause other downloads and streaming on your network.',
                    'Choose a download region closer to you in Settings.',
                    'Restart your router and the client.',
                ]],
            ],
        ],
        5 => [
            'title'   => 'How do I verify the integrity of game files?',
            'cat'     => 'Steam Client',
            'updated' => '2026-07-14',
            'body'    => [
                ['p'  => 'Verifying game files checks for missing or corrupted files and re-downloads them.'],
                ['ol' => [
                    'Open your Library and right-click the game.',
                    'Choose Properties, then Installed Files.',
                    'Select "Verify integrity of game files".',
                ]],
            ],
        ],
        6 => [
            'title'   => 'How do I post a review or guide?',
            'cat'     => 'Community',
            'updated' => '2026-11-05',
            'body'    => [
                ['p'  => 'Open the Community Hub of a game you own and use the "Post to the hub" button.'],
                ['ul' => ['Reviews need a title and a description.', 'Guides can have several sections and categories.']],
            ],
        ],
        7 => [
            'title'   => 'How do I report abusive content?',
            'cat'     => 'Community',
            'updated' => '2026-06-30',
            'body'    => [
                ['p' => 'Use the Report button on any post or comment. Our moderators review reports and remove content that breaks the Online Conduct Rules.'],
            ],
        ],
    ],
];