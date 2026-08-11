<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WhatsAppTemplateController extends Controller
{
    public function get(Request $request): JsonResponse
    {
        return response()->json([
            'data' => [
                [
                    'name' => 'reservation_confirmation',
                    'parameter_format' => 'NAMED',
                    'components' => [
                        [
                            'type' => 'HEADER',
                            'format' => 'IMAGE',
                            'example' => [
                                'header_handle' => [
                                    'https://scontent.whatsapp.net/v/t61...',
                                ],
                            ],
                        ],
                        [
                            'type' => 'BODY',
                            'text' => "*You're all set!*\n\nYour reservation for {{number_of_guests}} at Lucky Shrub Eatery on {{day}}, {{date}}, at {{time}}, is confirmed. See you then!",
                            'example' => [
                                'body_text_named_params' => [
                                    [
                                        'param_name' => 'number_of_guests',
                                        'example' => '4',
                                    ],
                                    [
                                        'param_name' => 'day',
                                        'example' => 'Saturday',
                                    ],
                                    [
                                        'param_name' => 'date',
                                        'example' => 'August 30th, 2025',
                                    ],
                                    [
                                        'param_name' => 'time',
                                        'example' => '7:30 pm',
                                    ],
                                ],
                            ],
                        ],
                        [
                            'type' => 'FOOTER',
                            'text' => 'Lucky Shrub Eatery: The Luckiest Eatery in Town!',
                        ],
                        [
                            'type' => 'BUTTONS',
                            'buttons' => [
                                [
                                    'type' => 'URL',
                                    'text' => 'Change reservation',
                                    'url' => 'https://www.luckyshrubeater.com/reservations',
                                ],
                                [
                                    'type' => 'PHONE_NUMBER',
                                    'text' => 'Call us',
                                    'phone_number' => '+16467043595',
                                ],
                                [
                                    'type' => 'QUICK_REPLY',
                                    'text' => 'Cancel reservation',
                                ],
                            ],
                        ],
                    ],
                    'language' => 'en_US',
                    'status' => 'APPROVED',
                    'category' => 'UTILITY',
                    'id' => '1387372356726668',
                ],
                [
                    'name' => 'coupon_expiration_reminder_number_vars',
                    'parameter_format' => 'POSITIONAL',
                    'components' => [
                        [
                            'type' => 'HEADER',
                            'format' => 'TEXT',
                            'text' => 'Act fast, {{1}}!',
                            'example' => [
                                'header_text' => [
                                    'Pablo',
                                ],
                            ],
                        ],
                        [
                            'type' => 'BODY',
                            'text' => "Just a quick reminder—your exclusive coupon code, {{1}}, *expires in only {{2}} days!* Don't miss out on our special deals. Use your code at checkout before it's too late.\n\nHappy shopping! 😃",
                            'example' => [
                                'body_text' => [
                                    [
                                        'SUMMER20',
                                        '10',
                                    ],
                                ],
                            ],
                        ],
                        [
                            'type' => 'FOOTER',
                            'text' => 'Lucky Shrub Succulents',
                        ],
                        [
                            'type' => 'BUTTONS',
                            'buttons' => [
                                [
                                    'type' => 'URL',
                                    'text' => 'See deals',
                                    'url' => 'https://www.luckyshrub.com/deals',
                                ],
                                [
                                    'type' => 'QUICK_REPLY',
                                    'text' => 'Unsubscribe',
                                ],
                            ],
                        ],
                    ],
                    'language' => 'en',
                    'status' => 'APPROVED',
                    'category' => 'MARKETING',
                    'sub_category' => 'CUSTOM',
                    'id' => '1304694804498707',
                ],
            ],
            'paging' => [
                'cursors' => [
                    'before' => 'QVFIU...',
                    'after' => 'QVFIU...',
                ],
                'next' => 'https://graph.facebook.com/v23.0/10229...',
            ],
        ], 200);
    }
}
