<?php

namespace Database\Seeders;

use App\Models\Blog;
use App\Models\BlogCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        $fashion = BlogCategory::firstOrCreate(
            ['slug' => 'fashion'],
            ['name' => 'Fashion']
        );

        $posts = [
            [
                'title' => 'Top Fashion Trends Every Woman Should Try This Season',
                'image' => 'cozastore/images/blog-04.jpg',
                'excerpt' => 'Discover the top fashion trends that can instantly refresh your wardrobe and give your daily style a modern touch.',
                'content' => '
                    <p>Fashion changes every season, but true style is about knowing what works for you and wearing it with confidence. This season, we are seeing a strong comeback of soft tailoring, wide-leg pants, neutral tones, and statement accessories.</p>

                    <p>Blazers are no longer limited to formal looks. They can now be styled with denim, sneakers, or even dresses for a smart casual outfit. Wide-leg trousers are also becoming a favorite choice because they combine comfort with elegance.</p>

                    <p>Another important trend is monochrome dressing. Wearing one color in different shades creates a clean and polished appearance. Beige, white, grey, and soft brown are leading the color story this season.</p>

                    <p>Accessories also play a major role. A structured handbag, bold sunglasses, or a pair of elegant heels can transform a simple outfit into a memorable one. The key is balance—let one piece stand out while keeping the rest of the outfit refined.</p>

                    <p>If you want to upgrade your style this season, start with timeless basics and add one or two trend-forward pieces. This will keep your wardrobe fashionable without losing versatility.</p>
                ',
                'is_featured' => true,
            ],
            [
                'title' => 'How to Build a Timeless Wardrobe with Simple Pieces',
                'image' => 'cozastore/images/blog-05.jpg',
                'excerpt' => 'A timeless wardrobe is built on classic essentials that never go out of style and work for every occasion.',
                'content' => '
                    <p>Building a timeless wardrobe does not mean owning dozens of clothes. It means choosing essential pieces that can be styled in many ways and remain elegant year after year.</p>

                    <p>Start with the basics: a white shirt, black trousers, blue jeans, a neutral blazer, a classic handbag, and comfortable shoes. These items create the foundation of a smart wardrobe and make daily styling easier.</p>

                    <p>The secret to a timeless wardrobe is quality over quantity. Instead of buying many trendy items, invest in fabrics and cuts that look sophisticated and last longer. A well-fitted dress or tailored blazer can be worn repeatedly without losing its value.</p>

                    <p>Color selection also matters. Neutral colors such as black, white, beige, and navy are easy to mix and match. Once your base wardrobe is ready, you can add seasonal pieces for variety.</p>

                    <p>A timeless wardrobe saves money, reduces decision fatigue, and gives you a polished look every day. Fashion trends come and go, but elegant basics always stay relevant.</p>
                ',
                'is_featured' => true,
            ],
            [
                'title' => 'The Best Accessories to Elevate Any Outfit',
                'image' => 'cozastore/images/blog-06.jpg',
                'excerpt' => 'Accessories can completely transform a simple outfit into a stylish statement with very little effort.',
                'content' => '
                    <p>Accessories are one of the easiest ways to make your outfit look more stylish. Even the simplest clothing can feel elevated with the right finishing touches.</p>

                    <p>Jewelry is often the first place to start. Minimal gold or silver pieces can add elegance, while chunky earrings or layered necklaces create a bolder fashion statement. Choose accessories that match the mood of your outfit.</p>

                    <p>Handbags are another essential detail. A structured bag gives a polished look, while a relaxed crossbody bag adds casual sophistication. Shoes also matter greatly—heels, loafers, and clean sneakers all send different style messages.</p>

                    <p>Belts, sunglasses, scarves, and watches are often overlooked, but they can completely change the overall appearance. A belt can define the waist, sunglasses can add attitude, and a silk scarf can make a basic outfit look luxurious.</p>

                    <p>If you want to improve your personal style, never underestimate the power of accessories. They are small details, but they make a big difference.</p>
                ',
                'is_featured' => true,
            ],
            [
                'title' => 'Street Style Inspiration for Modern Fashion Lovers',
                'image' => 'cozastore/images/blog-04.jpg',
                'excerpt' => 'Street style is where comfort meets creativity, giving fashion lovers freedom to express personality with confidence.',
                'content' => '
                    <p>Street style has become one of the strongest influences in modern fashion. It reflects real-life creativity, confidence, and personal identity more than runway rules.</p>

                    <p>Oversized shirts, relaxed denim, sneakers, crop jackets, and statement bags are common pieces in street-inspired looks. These items are comfortable, practical, and easy to style for daily life.</p>

                    <p>Layering is a major part of street fashion. You can mix textures, lengths, and colors to create depth and originality. For example, an oversized blazer over a simple T-shirt and jeans can look effortless yet stylish.</p>

                    <p>Street style also encourages individuality. Some people prefer sporty looks, while others mix classic pieces with edgy details. The goal is not perfection—it is personality.</p>

                    <p>If you love fashion but want more freedom, street style is the perfect place to explore your creativity and build a look that feels authentic to you.</p>
                ',
                'is_featured' => false,
            ],
            [
                'title' => 'Summer Fashion Essentials You Need in Your Closet',
                'image' => 'cozastore/images/blog-05.jpg',
                'excerpt' => 'From breezy dresses to lightweight fabrics, these summer essentials help you stay stylish and comfortable all season long.',
                'content' => '
                    <p>Summer fashion is all about lightness, ease, and comfort. The best summer wardrobe includes breathable fabrics, bright energy, and simple pieces that feel good in warm weather.</p>

                    <p>A flowy dress is one of the most important summer essentials. It is easy to wear, flattering, and suitable for both casual daytime outings and evening plans. Linen shirts and cotton trousers are also excellent choices for staying cool.</p>

                    <p>Neutral sandals, woven bags, and sunglasses bring a fresh summer feeling to any outfit. Light colors such as white, beige, sky blue, and soft pink are especially popular because they reflect the relaxed mood of the season.</p>

                    <p>Summer style does not need to be complicated. A simple outfit with the right fit and accessories can feel elegant and effortless. Focus on breathable materials and comfortable cuts.</p>

                    <p>With a few versatile essentials, you can create many beautiful summer looks without overcrowding your wardrobe.</p>
                ',
                'is_featured' => false,
            ],
            [
                'title' => 'How to Style Oversized Blazers in a Chic Way',
                'image' => 'cozastore/images/blog-06.jpg',
                'excerpt' => 'Oversized blazers are a modern fashion staple that can be styled casually or elegantly depending on the occasion.',
                'content' => '
                    <p>Oversized blazers have become a must-have piece in contemporary fashion. They are structured, stylish, and versatile enough to work across multiple looks.</p>

                    <p>For a casual outfit, pair an oversized blazer with denim jeans, a basic top, and sneakers. This creates a relaxed but polished appearance that works well for everyday wear.</p>

                    <p>For a more elegant look, style the blazer with tailored trousers or a fitted skirt. Add heels and a classic handbag to create a refined outfit suitable for meetings, dinners, or formal outings.</p>

                    <p>Belted blazers are another stylish option. Adding a belt helps define the waist and creates a more feminine silhouette without losing the blazer’s modern structure.</p>

                    <p>The beauty of oversized blazers is that they combine power and comfort. They are timeless, fashionable, and easy to include in both minimalist and trend-focused wardrobes.</p>
                ',
                'is_featured' => false,
            ],
            [
                'title' => 'Must Have Shoes That Complete Every Fashion Look',
                'image' => 'cozastore/images/blog-04.jpg',
                'excerpt' => 'The right shoes can define your whole outfit, making them one of the most important style investments.',
                'content' => '
                    <p>Shoes are not just a finishing touch—they are one of the most important parts of any outfit. The right pair can improve your posture, confidence, and overall style instantly.</p>

                    <p>Every wardrobe should include a few essential shoe styles: clean white sneakers, classic heels, elegant flats, and practical loafers. These options cover most occasions and work with a wide range of outfits.</p>

                    <p>White sneakers bring freshness and comfort to casual looks. Heels add polish and femininity, while loafers create a smart and balanced appearance. Flats are perfect for days when comfort is a priority without sacrificing style.</p>

                    <p>Color is also important. Neutral shades such as black, beige, nude, and white are usually the most practical because they pair easily with different clothing styles.</p>

                    <p>When choosing shoes, always think about both comfort and versatility. Fashion should look good, but it should also feel good.</p>
                ',
                'is_featured' => false,
            ],
            [
                'title' => 'Minimalist Fashion: Why Less Is More',
                'image' => 'cozastore/images/blog-05.jpg',
                'excerpt' => 'Minimalist fashion proves that simple lines, neutral tones, and smart styling can create a strong and elegant presence.',
                'content' => '
                    <p>Minimalist fashion is built on the idea that simplicity can be incredibly powerful. Instead of relying on loud prints or excessive details, minimalist style focuses on clean silhouettes and intentional choices.</p>

                    <p>Neutral colors are the heart of this aesthetic. White, black, grey, cream, and beige create a calm and polished wardrobe that is easy to style. Well-tailored pieces also matter more than quantity or decoration.</p>

                    <p>A minimalist look often includes straight trousers, classic shirts, long coats, simple dresses, and understated accessories. The result is modern, sophisticated, and timeless.</p>

                    <p>One of the biggest benefits of minimalist fashion is that it makes getting dressed easier. Every piece works with the others, and the overall wardrobe feels more organized and purposeful.</p>

                    <p>If you want your style to look elegant and effortless, minimalist fashion is one of the best directions to follow.</p>
                ',
                'is_featured' => false,
            ],
            [
                'title' => 'How Colors Influence Your Fashion Style',
                'image' => 'cozastore/images/blog-06.jpg',
                'excerpt' => 'Color plays a powerful role in fashion, shaping mood, confidence, and the impression your outfit gives to others.',
                'content' => '
                    <p>Color has a strong effect on the way an outfit feels and the message it sends. In fashion, colors are not only decorative—they help express mood, identity, and energy.</p>

                    <p>Black often represents elegance and authority. White feels clean and fresh. Beige and brown create warmth and softness, while bright colors such as red or green add confidence and attention.</p>

                    <p>If you are not used to wearing bold colors, start with accessories or one statement piece. A colorful bag, shoes, or blazer can add personality without overwhelming the look.</p>

                    <p>Understanding your preferred color palette can make shopping easier and help you create a wardrobe that feels more cohesive. It also helps you communicate your personal style more clearly.</p>

                    <p>Fashion is not only about shape and fabric—color is one of the strongest tools you can use to create impact.</p>
                ',
                'is_featured' => false,
            ],
            [
                'title' => 'Fashion Tips for Looking Elegant Every Day',
                'image' => 'cozastore/images/blog-04.jpg',
                'excerpt' => 'Everyday elegance comes from fit, simplicity, grooming, and choosing pieces that make you feel confident and refined.',
                'content' => '
                    <p>Looking elegant every day does not require expensive clothes or complicated styling. True elegance often comes from thoughtful choices and attention to detail.</p>

                    <p>The first rule is fit. Clothes that fit properly always look better than expensive pieces in the wrong size. Tailoring can make a huge difference in how polished your wardrobe appears.</p>

                    <p>The second rule is simplicity. Choose clean shapes, balanced proportions, and colors that work together. Avoid over-accessorizing, and instead let one or two elements stand out.</p>

                    <p>Good grooming, neat shoes, and a well-maintained handbag also contribute to an elegant overall image. Fashion is not only about clothing—it is about presentation.</p>

                    <p>When you build habits around fit, quality, and confidence, elegance becomes part of your daily style naturally.</p>
                ',
                'is_featured' => false,
            ],
        ];

        foreach ($posts as $index => $post) {
            Blog::create([
                'blog_category_id' => $fashion->id,
                'title' => $post['title'],
                'slug' => Str::slug($post['title']),
                'image' => $post['image'],
                'excerpt' => $post['excerpt'],
                'content' => $post['content'],
                'author' => 'Creative Store',
                'is_featured' => $post['is_featured'],
                'status' => true,
                'views' => rand(15, 250),
                'published_at' => now()->subDays($index + 1),
            ]);
        }
    }
}
