<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $page = Page::query()->updateOrCreate(
                ['slug' => 'about'],
                ['is_active' => true]
            );

            $page->translations()->updateOrCreate(
                ['locale' => 'ar'],
                [
                    'title' => 'من نحن',
                    'content' => json_encode([
                        'hero_title' => 'من نحن',
                        'story_title' => 'قصتنا',
                        'story_text_1' => 'في App Store بدأنا برؤية واضحة: تقديم تجربة تسوق إلكتروني عصرية، سهلة، وآمنة تلبي احتياجات العملاء وتواكب تطور التجارة الرقمية. نحن نؤمن أن التسوق عبر الإنترنت يجب أن يكون أكثر من مجرد عملية شراء، بل تجربة متكاملة تجمع بين الجودة، الراحة، والثقة.',
                        'story_text_2' => 'من خلال منصتنا، نحرص على توفير مجموعة متنوعة من المنتجات المختارة بعناية لتناسب مختلف الأذواق والاحتياجات، مع واجهة استخدام سلسة وخدمة موثوقة تجعل العميل يشعر بالسهولة في كل خطوة، من تصفح المنتجات وحتى إتمام الطلب.',
                        'story_text_3' => 'في App Store نعمل باستمرار على تطوير خدماتنا وتحسين تجربة المستخدم، لأن هدفنا ليس فقط بيع المنتجات، بل بناء علاقة طويلة الأمد مع عملائنا قائمة على المصداقية، الجودة، والالتزام.',
                        'mission_title' => 'رسالتنا',
                        'mission_text' => 'رسالتنا في App Store هي تقديم تجربة تسوق إلكتروني مميزة تجمع بين تنوع المنتجات، سهولة الاستخدام، سرعة الخدمة، وأعلى معايير الجودة. نسعى لأن نكون الوجهة المفضلة للعملاء الباحثين عن الثقة والاحترافية في عالم التجارة الإلكترونية، مع التزام دائم بالابتكار وتقديم قيمة حقيقية في كل طلب.',
                        'quote_text' => 'نحن لا نبيع منتجات فقط، بل نصنع تجربة تسوق تمنح العميل ثقة وراحة في كل مرة يختارنا فيها.',
                        'quote_author' => 'App Store',
                        'image_1' => 'images/about-01.jpg',
                        'image_2' => 'images/about-02.jpg',
                    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                ]
            );

            $page->translations()->updateOrCreate(
                ['locale' => 'en'],
                [
                    'title' => 'About',
                    'content' => json_encode([
                        'hero_title' => 'About Us',
                        'story_title' => 'Our Story',
                        'story_text_1' => 'At App Store, we started with a clear vision: to create a modern, seamless, and secure eCommerce experience that meets customer needs and keeps pace with the fast-growing digital world. We believe online shopping should be more than just buying products — it should be a complete and enjoyable experience built on quality, convenience, and trust.',
                        'story_text_2' => 'Through our platform, we carefully provide a wide range of products selected to match different tastes and lifestyles, supported by a smooth user experience and reliable service that makes every step simple, from browsing products to completing orders.',
                        'story_text_3' => 'At App Store, we continuously work on improving our services and enhancing the customer journey, because our goal is not only to sell products, but to build long-term relationships with our customers based on credibility, quality, and commitment.',
                        'mission_title' => 'Our Mission',
                        'mission_text' => 'Our mission at App Store is to deliver an outstanding eCommerce experience that combines product variety, ease of use, fast service, and high quality standards. We strive to become the preferred destination for customers seeking trust, convenience, and professionalism in online shopping, while continuously innovating to deliver real value in every order.',
                        'quote_text' => 'We do not simply sell products — we create a shopping experience that gives customers confidence and comfort every time they choose us.',
                        'quote_author' => 'App Store',
                        'image_1' => 'images/about-01.jpg',
                        'image_2' => 'images/about-02.jpg',
                    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                ]
            );
                 /*
            |--------------------------------------------------------------------------
            | CONTACT PAGE
            |--------------------------------------------------------------------------
            */
            $contact = Page::query()->updateOrCreate(
                ['slug' => 'contact'],
                ['is_active' => true]
            );

            $contact->translations()->updateOrCreate(
                ['locale' => 'ar'],
                [
                    'title' => 'تواصل معنا',
                    'content' => json_encode([
                        'hero_title' => 'تواصل معنا',
                        'form_title' => 'ارسل لنا رسالة',
                        'address_title' => 'العنوان',
                        'address_text' => 'الرياض، المملكة العربية السعودية',
                        'phone_title' => 'تحدث معنا',
                        'phone_text' => '+966 5X XXX XXXX',
                        'email_title' => 'الدعم الفني',
                        'email_text' => 'support@appstore.com',
                        'map_x' => '24.7136',
                        'map_y' => '46.6753',
                        'map_zoom' => '11',
                    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                ]
            );

            $contact->translations()->updateOrCreate(
                ['locale' => 'en'],
                [
                    'title' => 'Contact',
                    'content' => json_encode([
                        'hero_title' => 'Contact Us',
                        'form_title' => 'Send Us A Message',
                        'address_title' => 'Address',
                        'address_text' => 'Riyadh, Saudi Arabia',
                        'phone_title' => "Let's Talk",
                        'phone_text' => '+966 5X XXX XXXX',
                        'email_title' => 'Support Email',
                        'email_text' => 'support@appstore.com',
                        'map_x' => '24.7136',
                        'map_y' => '46.6753',
                        'map_zoom' => '11',
                    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                ]
            );

       
        });
    }
}
