<?php

namespace Database\Seeders;

/**
 * Bilingual blog posts for BlogPostsSeeder.
 *
 * Images reuse the catalogue photos downloaded by DemoCatalogSeeder
 * (storage/app/public/products/catalog). In the bodies, {{slug|label}}
 * becomes a link to that product in the post's language.
 */
final class BlogPostsData
{
    public static function categories(): array
    {
        return [
            'fashion'     => ['Fashion', 'الموضة'],
            'style-guide' => ['Style Guides', 'أدلة الأناقة'],
            'fragrance'   => ['Fragrance', 'عالم العطور'],
            'accessories' => ['Accessories', 'الإكسسوارات'],
            'mens-style'  => ['Men’s Style', 'أناقة الرجل'],
            'care'        => ['Care & Quality', 'العناية والجودة'],
            'occasions'   => ['Occasions & Seasons', 'مناسبات ومواسم'],
        ];
    }

    public static function posts(): array
    {
        return array_merge(self::part1(), self::part2(), self::part3(), self::part4());
    }

    private static function part1(): array
    {
        return [
            [
                'slug' => 'how-to-choose-your-size-online',
                'category' => 'style-guide',
                'photo' => 'photo-1598554747436-c9293d6a588f',
                'featured' => true,
                'days_ago' => 3,
                'en' => [
                    'title' => 'Never Guess Again: How to Choose Your Size Online',
                    'excerpt' => 'A tape measure, your favourite piece and five minutes. That is all it takes to order the right size the first time.',
                    'content' => <<<'HTML'
<p>Most returns have nothing to do with taste. The colour was right, the style was right, but the fit was not. The good news: choosing your size online is a skill, and it takes about five minutes to learn.</p>
<h2>1. Measure the piece you already love</h2>
<p>Lay your best-fitting shirt or jeans flat and measure chest (armpit to armpit), waist and length. Compare those numbers with the product, not with your body. It is the most reliable trick there is.</p>
<h2>2. Know your three key numbers</h2>
<ul>
<li><strong>Chest:</strong> around the fullest part, tape level under the arms.</li>
<li><strong>Waist:</strong> at your natural waistline, one finger of space under the tape.</li>
<li><strong>Hips:</strong> around the widest point, feet together.</li>
</ul>
<h2>3. Read the fabric, not just the label</h2>
<p>Cotton with elastane forgives an extra centimetre; linen and denim without stretch do not. Knitwear such as our {{cable-knit-cream-sweater|cable knit sweater}} is designed to sit relaxed, so stay with your usual size.</p>
<blockquote>Between two sizes? Pick the larger one for jackets and knits, and the smaller one for stretch denim.</blockquote>
<h2>4. Shoes: measure in the evening</h2>
<p>Feet swell slightly during the day. Stand on paper, mark heel and longest toe, measure, then add half a centimetre. Trainers like the {{white-leather-trainers|white leather trainers}} run true to size.</p>
<p>Still unsure? Message us before you order and we will check the exact measurements of the piece for you.</p>
HTML,
                ],
                'ar' => [
                    'title' => 'وداعًا للتخمين: كيف تختار مقاسك الصحيح أونلاين',
                    'excerpt' => 'شريط قياس، وقطعتك المفضلة، وخمس دقائق فقط. هذا كل ما تحتاجه لتطلب المقاس الصحيح من أول مرة.',
                    'content' => <<<'HTML'
<p>أغلب المرتجعات لا علاقة لها بالذوق؛ اللون مناسب والتصميم رائع، لكن المقاس لم يكن صحيحًا. الخبر الجيد أن اختيار المقاس أونلاين مهارة يمكنك تعلّمها في خمس دقائق.</p>
<h2>١. قِس القطعة التي تحبها بالفعل</h2>
<p>افرد أفضل قميص أو جينز يناسبك على سطح مستوٍ، وقِس الصدر (من الإبط إلى الإبط) والخصر والطول. قارن هذه الأرقام بالمنتج لا بجسمك؛ فهذه أدق طريقة على الإطلاق.</p>
<h2>٢. اعرف أرقامك الثلاثة</h2>
<ul>
<li><strong>الصدر:</strong> حول أعرض جزء، والشريط مستوٍ تحت الذراعين.</li>
<li><strong>الخصر:</strong> عند خط الخصر الطبيعي، مع ترك مساحة إصبع تحت الشريط.</li>
<li><strong>الأرداف:</strong> حول أعرض نقطة والقدمان متلاصقتان.</li>
</ul>
<h2>٣. اقرأ نوع القماش لا الملصق فقط</h2>
<p>القطن المخلوط بالإيلاستين يسامحك في سنتيمتر إضافي، أما الكتان والجينز غير المطاطي فلا. والتريكو مثل {{cable-knit-cream-sweater|كنزة التريكو الكريمية}} مصمم ليكون مريحًا، فالتزم بمقاسك المعتاد.</p>
<blockquote>بين مقاسين؟ اختر الأكبر في الجاكيتات والتريكو، والأصغر في الجينز المطاطي.</blockquote>
<h2>٤. الأحذية: قِس قدمك في المساء</h2>
<p>تنتفخ القدم قليلًا خلال اليوم. قف على ورقة، وحدّد الكعب وأطول إصبع، ثم أضف نصف سنتيمتر. والأحذية الرياضية مثل {{white-leather-trainers|الحذاء الجلدي الأبيض}} مقاسها مطابق للمعتاد.</p>
<p>ما زلت مترددًا؟ راسلنا قبل الطلب وسنقيس لك القطعة بدقة.</p>
HTML,
                ],
            ],
            [
                'slug' => 'ten-piece-capsule-wardrobe',
                'category' => 'style-guide',
                'photo' => 'photo-1789110519342-6d3f6b07a749',
                'featured' => true,
                'days_ago' => 7,
                'en' => [
                    'title' => 'The 10-Piece Capsule: Thirty Outfits From One Small Rail',
                    'excerpt' => 'Fewer clothes, more outfits. Here is the exact capsule we would build, and the maths behind it.',
                    'content' => <<<'HTML'
<p>A capsule wardrobe is not about owning less for the sake of it. It is about owning pieces that all talk to each other, so getting dressed takes one minute instead of fifteen.</p>
<h2>The ten pieces</h2>
<ol>
<li>A {{beige-slip-dress|beige slip dress}}: layer it, belt it, wear it alone.</li>
<li>A {{classic-black-midi-dress|black midi dress}} for evenings.</li>
<li>One relaxed knit, like the {{beige-v-neck-knit-top|beige V-neck}}.</li>
<li>{{high-rise-mom-jeans|High-rise jeans}} in a mid wash.</li>
<li>A crisp white shirt.</li>
<li>A light cardigan.</li>
<li>A structured bag such as the {{cognac-leather-tote|cognac tote}}.</li>
<li>Clean white trainers.</li>
<li>One pair of heels.</li>
<li>A single statement piece of jewellery.</li>
</ol>
<h2>Why it works</h2>
<p>Every top matches every bottom, and the shoes and bag are neutral. Five tops and bottoms combined with two pairs of shoes already give you more than thirty distinct looks.</p>
<blockquote>Rule of thumb: three neutrals, one accent colour, one texture you love.</blockquote>
<h2>Make it yours</h2>
<p>Swap the accent every season: emerald in winter, coral in summer. The base stays; only the mood changes.</p>
HTML,
                ],
                'ar' => [
                    'title' => 'خزانة العشر قطع: ثلاثون إطلالة من رف صغير',
                    'excerpt' => 'ملابس أقل وإطلالات أكثر. هذه هي الخزانة التي نقترحها بالتفصيل، والحسبة التي تجعلها تعمل.',
                    'content' => <<<'HTML'
<p>الخزانة المختصرة لا تعني التقشف، بل امتلاك قطع تتناسق مع بعضها، فيصبح اختيار ملابسك دقيقة واحدة بدل ربع ساعة.</p>
<h2>القطع العشر</h2>
<ol>
<li>{{beige-slip-dress|فستان ساتان بيج}}: ارتديه وحده أو بطبقة فوقه أو مع حزام.</li>
<li>{{classic-black-midi-dress|فستان ميدي أسود}} للسهرات.</li>
<li>قطعة تريكو مريحة مثل {{beige-v-neck-knit-top|البلوزة البيج بفتحة V}}.</li>
<li>{{high-rise-mom-jeans|جينز بخصر عالٍ}} بدرجة متوسطة.</li>
<li>قميص أبيض أنيق.</li>
<li>كارديغان خفيف.</li>
<li>حقيبة متماسكة مثل {{cognac-leather-tote|حقيبة التوت الجلدية}}.</li>
<li>حذاء رياضي أبيض.</li>
<li>حذاء بكعب واحد.</li>
<li>قطعة مجوهرات لافتة واحدة.</li>
</ol>
<h2>لماذا تنجح؟</h2>
<p>كل قطعة علوية تناسب كل قطعة سفلية، والحذاء والحقيبة بألوان محايدة. خمس قطع علوية وسفلية مع زوجين من الأحذية تمنحك أكثر من ثلاثين إطلالة مختلفة.</p>
<blockquote>القاعدة الذهبية: ثلاثة ألوان محايدة، ولون مميز واحد، وخامة واحدة تعشقها.</blockquote>
<h2>اجعلها خاصة بك</h2>
<p>بدّل اللون المميز كل موسم: الزمردي شتاءً والمرجاني صيفًا. الأساس ثابت، والمزاج فقط يتغير.</p>
HTML,
                ],
            ],
            [
                'slug' => 'colour-psychology-what-your-outfit-says',
                'category' => 'fashion',
                'photo' => 'photo-1789110853844-d1a86b2c5b9b',
                'featured' => false,
                'days_ago' => 11,
                'en' => [
                    'title' => 'Colour Psychology: What Your Outfit Says Before You Do',
                    'excerpt' => 'People decide how they feel about you in seconds, and colour does much of the talking. Use it on purpose.',
                    'content' => <<<'HTML'
<p>Before anyone hears your voice, they have already read your colours. That is not superstition; it is how our brains shortcut first impressions.</p>
<h2>The quick guide</h2>
<table>
<thead><tr><th>Colour</th><th>It says</th><th>Wear it for</th></tr></thead>
<tbody>
<tr><td>Navy</td><td>Trust, calm</td><td>Interviews, first meetings</td></tr>
<tr><td>Emerald</td><td>Confidence, growth</td><td>Presentations, celebrations</td></tr>
<tr><td>Blush</td><td>Warmth, approachability</td><td>Family gatherings</td></tr>
<tr><td>Black</td><td>Authority, elegance</td><td>Evenings, formal events</td></tr>
<tr><td>White</td><td>Freshness, clarity</td><td>Summer days, brunch</td></tr>
</tbody>
</table>
<h2>Try it</h2>
<p>Our {{emerald-sleeveless-dress|emerald dress}} is the easiest way into colour: rich enough to stand out, deep enough to stay elegant. For a softer statement, the {{blush-pink-evening-dress|blush evening dress}} reads warm and inviting.</p>
<blockquote>New to colour? Start with accessories: a bag or a scarf is a low-risk experiment.</blockquote>
HTML,
                ],
                'ar' => [
                    'title' => 'سيكولوجية الألوان: ماذا تقول ملابسك عنك قبل أن تتكلم؟',
                    'excerpt' => 'يكوّن الناس انطباعهم عنك في ثوانٍ، والألوان تتكلم نيابة عنك. استخدمها بوعي.',
                    'content' => <<<'HTML'
<p>قبل أن يسمع أحد صوتك، يكون قد قرأ ألوانك. ليست خرافة، بل طريقة يختصر بها العقل الانطباع الأول.</p>
<h2>الدليل السريع</h2>
<table>
<thead><tr><th>اللون</th><th>رسالته</th><th>متى ترتديه</th></tr></thead>
<tbody>
<tr><td>الكحلي</td><td>الثقة والهدوء</td><td>المقابلات والاجتماعات الأولى</td></tr>
<tr><td>الزمردي</td><td>الثقة والنمو</td><td>العروض التقديمية والاحتفالات</td></tr>
<tr><td>الوردي الهادئ</td><td>الدفء والقرب</td><td>التجمعات العائلية</td></tr>
<tr><td>الأسود</td><td>الحضور والأناقة</td><td>السهرات والمناسبات الرسمية</td></tr>
<tr><td>الأبيض</td><td>الانتعاش والوضوح</td><td>أيام الصيف ولقاءات الصباح</td></tr>
</tbody>
</table>
<h2>جرّب بنفسك</h2>
<p>{{emerald-sleeveless-dress|الفستان الزمردي}} أسهل مدخل لعالم الألوان: لافت بما يكفي، وعميق بما يحافظ على الأناقة. ولإطلالة أنعم، يمنحك {{blush-pink-evening-dress|فستان السهرة الوردي}} حضورًا دافئًا ومريحًا.</p>
<blockquote>جديد على الألوان؟ ابدأ بالإكسسوارات؛ الحقيبة أو الوشاح تجربة بلا مخاطرة.</blockquote>
HTML,
                ],
            ],
        ];
    }

    private static function part2(): array
    {
        return [
            [
                'slug' => 'the-art-of-fragrance-layering',
                'category' => 'fragrance',
                'photo' => 'photo-1594035910387-fea47794261f',
                'featured' => true,
                'days_ago' => 5,
                'en' => [
                    'title' => 'The Art of Layering: A Scent That Lasts Until Midnight',
                    'excerpt' => 'Layering is a Gulf tradition for a reason. Learn the order, the pairings and the one mistake that ruins both scents.',
                    'content' => <<<'HTML'
<p>In the Gulf, fragrance is never a single spray. It is built in layers, from skin to fabric, so it unfolds through the day instead of disappearing by lunch.</p>
<h2>The order matters</h2>
<ol>
<li><strong>Base:</strong> an unscented or lightly scented moisturiser. Fragrance clings to hydrated skin.</li>
<li><strong>Heart:</strong> a soft musk such as {{pure-musk-eau-de-toilette|Pure Musk}} on the wrists and neck.</li>
<li><strong>Statement:</strong> a deeper scent like {{oud-noir-eau-de-parfum|Oud Noir}} on the chest and behind the ears.</li>
<li><strong>Finish:</strong> one light mist on your scarf or collar; fabric holds scent for hours.</li>
</ol>
<h2>Pairings that always work</h2>
<ul>
<li>Musk + rose: soft and romantic.</li>
<li>Oud + saffron: warm and regal. Try {{golden-saffron-parfum|Golden Saffron}}.</li>
<li>Amber + vanilla: cosy evenings.</li>
</ul>
<blockquote>The one mistake: never rub your wrists together. It breaks the top notes and flattens the whole composition.</blockquote>
HTML,
                ],
                'ar' => [
                    'title' => 'فن طبقات العطر: رائحة تبقى معك حتى منتصف الليل',
                    'excerpt' => 'تنسيق العطور عادة خليجية أصيلة لسبب وجيه. تعرّف على الترتيب الصحيح، وأجمل التوليفات، والخطأ الذي يفسد العطرين معًا.',
                    'content' => <<<'HTML'
<p>في الخليج، العطر ليس رشة واحدة؛ بل طبقات تُبنى من البشرة إلى القماش، فتتفتح الرائحة على مدار اليوم بدل أن تختفي قبل الظهر.</p>
<h2>الترتيب هو السر</h2>
<ol>
<li><strong>القاعدة:</strong> مرطب بلا رائحة أو برائحة خفيفة؛ فالعطر يثبت أكثر على البشرة الرطبة.</li>
<li><strong>القلب:</strong> مسك ناعم مثل {{pure-musk-eau-de-toilette|عطر المسك النقي}} على المعصمين والرقبة.</li>
<li><strong>البصمة:</strong> عطر أعمق مثل {{oud-noir-eau-de-parfum|عود نوار}} على الصدر وخلف الأذنين.</li>
<li><strong>اللمسة الأخيرة:</strong> رشة خفيفة على الشماغ أو الياقة؛ فالقماش يحتفظ بالرائحة لساعات.</li>
</ol>
<h2>توليفات لا تخيب</h2>
<ul>
<li>المسك مع الورد: ناعمة ورومانسية.</li>
<li>العود مع الزعفران: دافئة وملكية، جرّب {{golden-saffron-parfum|عطر الزعفران الذهبي}}.</li>
<li>العنبر مع الفانيليا: لأمسيات الشتاء.</li>
</ul>
<blockquote>الخطأ الشائع: لا تفرك معصميك ببعضهما، فذلك يكسر النوتات العليا ويُفقد التركيبة جمالها.</blockquote>
HTML,
                ],
            ],
            [
                'slug' => 'summer-vs-winter-fragrances-saudi-climate',
                'category' => 'fragrance',
                'photo' => 'photo-1543422655-ac1c6ca993ed',
                'featured' => false,
                'days_ago' => 16,
                'en' => [
                    'title' => 'Summer Scents vs Winter Scents: Choosing for the Saudi Climate',
                    'excerpt' => 'Heat amplifies fragrance and cold mutes it. Here is how to build a scent wardrobe that works at 45°C and at 10°C.',
                    'content' => <<<'HTML'
<p>A perfume that feels perfect in a December evening in Riyadh can feel heavy at noon in July. Temperature changes how scent molecules evaporate, so your wardrobe of fragrances should change too.</p>
<h2>Summer: light, fresh, close to the skin</h2>
<p>In heat, choose citrus, aquatic and light musk notes, and use fewer sprays. {{citrus-bloom|Citrus Bloom}} and {{aqua-marine-cologne|Aqua Marine}} stay fresh without overwhelming a room.</p>
<h2>Winter: warm, deep, long-lasting</h2>
<p>Cool air lets richer notes shine: oud, amber, saffron and woods. {{white-amber-parfum|White Amber}} and {{cedar-and-vetiver|Cedar and Vetiver}} are made for evenings outdoors.</p>
<h2>Smart habits</h2>
<ul>
<li>Store bottles away from sunlight and car dashboards.</li>
<li>In summer, apply to pulse points under clothing.</li>
<li>Keep a travel size of your daytime scent in your bag.</li>
</ul>
HTML,
                ],
                'ar' => [
                    'title' => 'عطور الصيف وعطور الشتاء: اختيارك المناسب لأجواء السعودية',
                    'excerpt' => 'الحرارة تضخّم العطر والبرودة تهدّئه. إليك كيف تبني مجموعة عطور تناسب ٤٥ درجة و١٠ درجات.',
                    'content' => <<<'HTML'
<p>العطر الذي يبدو مثاليًا في أمسية من ديسمبر في الرياض قد يصبح ثقيلًا في ظهيرة يوليو. فالحرارة تغيّر سرعة تبخر جزيئات العطر، ولهذا يجب أن تتغير مجموعتك مع الفصول.</p>
<h2>الصيف: خفيف ومنعش وقريب من البشرة</h2>
<p>في الحر اختر النوتات الحمضية والبحرية والمسك الخفيف، وقلّل عدد الرشات. {{citrus-bloom|عطر سيتروس بلوم}} و{{aqua-marine-cologne|كولونيا أكوا مارين}} يبقيان منعشين دون أن يملآ المكان.</p>
<h2>الشتاء: دافئ وعميق وثابت</h2>
<p>الهواء البارد يُبرز النوتات الغنية: العود والعنبر والزعفران والأخشاب. {{white-amber-parfum|العنبر الأبيض}} و{{cedar-and-vetiver|الأرز والفيتيفر}} مثاليان للجلسات الخارجية.</p>
<h2>عادات ذكية</h2>
<ul>
<li>احفظ الزجاجات بعيدًا عن الشمس وطبلون السيارة.</li>
<li>في الصيف ضع العطر على نقاط النبض تحت الملابس.</li>
<li>احمل حجم سفر من عطرك اليومي في حقيبتك.</li>
</ul>
HTML,
                ],
            ],
            [
                'slug' => 'the-right-watch-for-every-occasion',
                'category' => 'accessories',
                'photo' => 'photo-1523170335258-f5ed11844a49',
                'featured' => false,
                'days_ago' => 9,
                'en' => [
                    'title' => 'One Wrist, Four Occasions: The Right Watch Every Time',
                    'excerpt' => 'Dress, sport, diver or chronograph? A simple map to the watch that fits the moment.',
                    'content' => <<<'HTML'
<p>A watch is the one accessory people notice in a handshake. Matching it to the occasion is easier than it looks.</p>
<h2>The office</h2>
<p>Clean dial, leather or steel strap, no more than 40 mm. The {{white-dial-steel-watch|white dial steel watch}} disappears under a cuff and still looks sharp.</p>
<h2>The wedding or formal dinner</h2>
<p>Thin case, simple hands, a hint of gold. The {{two-tone-dress-watch|two-tone dress watch}} is built for this.</p>
<h2>The weekend</h2>
<p>Go bolder: a {{steel-chronograph|chronograph}} or a {{gold-dial-diver|diver}} pairs with polos, denim and trainers.</p>
<h2>The heirloom</h2>
<p>An automatic movement such as the {{heritage-automatic-watch|Heritage Automatic}} needs no battery and can be passed on.</p>
<blockquote>Match metals: a gold watch looks best with gold buckles and jewellery.</blockquote>
HTML,
                ],
                'ar' => [
                    'title' => 'معصم واحد وأربع مناسبات: الساعة المناسبة في كل مرة',
                    'excerpt' => 'ساعة رسمية أم رياضية أم غوص أم كرونوغراف؟ دليل بسيط للساعة التي تليق باللحظة.',
                    'content' => <<<'HTML'
<p>الساعة هي الإكسسوار الذي يلاحظه الناس مع أول مصافحة، واختيارها حسب المناسبة أسهل مما تتوقع.</p>
<h2>للعمل</h2>
<p>ميناء بسيط وسوار جلدي أو معدني، ومقاس لا يتجاوز ٤٠ ملم. {{white-dial-steel-watch|الساعة الستيل بالميناء الأبيض}} تختفي تحت الكم وتبقى أنيقة.</p>
<h2>للأعراس والعشاء الرسمي</h2>
<p>علبة رفيعة وعقارب بسيطة ولمسة ذهبية. {{two-tone-dress-watch|ساعة السهرة ثنائية اللون}} صُممت لهذه اللحظات.</p>
<h2>لعطلة نهاية الأسبوع</h2>
<p>كن أكثر جرأة: {{steel-chronograph|كرونوغراف ستيل}} أو {{gold-dial-diver|ساعة غوص بميناء ذهبي}} تناسب البولو والجينز والأحذية الرياضية.</p>
<h2>الساعة التي تُورَّث</h2>
<p>الحركة الأوتوماتيكية مثل {{heritage-automatic-watch|هيريتج أوتوماتيك}} لا تحتاج بطارية، ويمكن أن تنتقل من جيل إلى جيل.</p>
<blockquote>نسّق المعادن: الساعة الذهبية أجمل مع إبزيم ومجوهرات ذهبية.</blockquote>
HTML,
                ],
            ],
            [
                'slug' => 'sunglasses-for-your-face-shape',
                'category' => 'accessories',
                'photo' => 'photo-1559070081-648fb00b2ed1',
                'featured' => false,
                'days_ago' => 20,
                'en' => [
                    'title' => 'Find Your Frame: Sunglasses for Every Face Shape',
                    'excerpt' => 'The right frame balances your features. Find your face shape in thirty seconds and the styles that flatter it.',
                    'content' => <<<'HTML'
<p>The secret to great sunglasses is contrast: the frame should balance your face, not repeat it.</p>
<h2>Find your shape</h2>
<p>Pull your hair back, look in a mirror and trace your outline with a washable marker. Is it widest at the forehead, the cheekbones or the jaw?</p>
<ul>
<li><strong>Round face:</strong> angular frames add definition. Try the {{classic-black-square-sunglasses|black square}}.</li>
<li><strong>Square face:</strong> rounded or aviator shapes soften the jaw, like the {{silver-aviator|silver aviator}}.</li>
<li><strong>Oval face:</strong> lucky you, almost everything works. The {{tortoiseshell-wayfarer|tortoiseshell wayfarer}} is a safe classic.</li>
<li><strong>Heart-shaped face:</strong> lighter or rimless bottoms balance a wider forehead, such as the {{smoke-lens-clear-frame|clear frame}}.</li>
</ul>
<blockquote>Under the Saudi sun, choose lenses with full UV400 protection; dark tint alone does not protect your eyes.</blockquote>
HTML,
                ],
                'ar' => [
                    'title' => 'اعثر على إطارك: نظارات شمسية لكل شكل وجه',
                    'excerpt' => 'الإطار الصحيح يوازن ملامحك. حدّد شكل وجهك في ثلاثين ثانية واعرف التصاميم التي تناسبك.',
                    'content' => <<<'HTML'
<p>سر النظارة الشمسية المثالية هو التباين: الإطار يوازن ملامح وجهك ولا يكررها.</p>
<h2>حدّد شكل وجهك</h2>
<p>اجمع شعرك للخلف، وانظر في المرآة وارسم حدود وجهك بقلم قابل للمسح. هل أعرض نقطة هي الجبهة أم عظام الخد أم الفك؟</p>
<ul>
<li><strong>الوجه الدائري:</strong> الإطارات الزاوية تمنحه تحديدًا، جرّب {{classic-black-square-sunglasses|النظارة المربعة السوداء}}.</li>
<li><strong>الوجه المربع:</strong> الإطارات الدائرية أو الطيار تُنعّم الفك، مثل {{silver-aviator|نظارة الطيار الفضية}}.</li>
<li><strong>الوجه البيضاوي:</strong> محظوظ، فكل شيء تقريبًا يناسبك، و{{tortoiseshell-wayfarer|نظارة الويفيرر بلون صدف السلحفاة}} خيار كلاسيكي آمن.</li>
<li><strong>الوجه على شكل قلب:</strong> الإطارات الخفيفة من الأسفل توازن الجبهة العريضة، مثل {{smoke-lens-clear-frame|النظارة بالإطار الشفاف}}.</li>
</ul>
<blockquote>تحت شمس السعودية اختر عدسات بحماية كاملة UV400؛ فاللون الداكن وحده لا يحمي عينيك.</blockquote>
HTML,
                ],
            ],
        ];
    }

    private static function part3(): array
    {
        return [
            [
                'slug' => 'mens-office-style-guide',
                'category' => 'mens-style',
                'photo' => 'photo-1602810318383-e386cc2a3ccf',
                'featured' => true,
                'days_ago' => 6,
                'en' => [
                    'title' => 'Sharp Without Trying: The Modern Man’s Office Wardrobe',
                    'excerpt' => 'Five shirts, two trousers, one watch. How to look put-together every working day without thinking about it.',
                    'content' => <<<'HTML'
<p>Office style today is less about suits and more about fit, fabric and a few reliable rules.</p>
<h2>The shirt rotation</h2>
<p>Own five shirts and you never repeat in a week: two whites like the {{crisp-white-dress-shirt|crisp white dress shirt}}, one {{sky-blue-oxford-shirt|sky blue Oxford}}, one {{navy-button-up-shirt|navy button-up}} and one subtle stripe.</p>
<h2>Fit checklist</h2>
<ul>
<li>Shoulder seam sits exactly on your shoulder bone.</li>
<li>You can fit two fingers inside the buttoned collar.</li>
<li>Cuffs end where your wrist meets your hand.</li>
</ul>
<h2>Finishing touches</h2>
<p>Match belt and shoes, keep the watch simple, and choose one fragrance that stays close to the skin. The office is not the place for a statement scent.</p>
<blockquote>Thursday rule: swap the dress shirt for a {{classic-pique-polo|piqué polo}} and keep everything else the same.</blockquote>
HTML,
                ],
                'ar' => [
                    'title' => 'أناقة بلا مجهود: خزانة الرجل العصري للعمل',
                    'excerpt' => 'خمسة قمصان وبنطالان وساعة واحدة. كيف تبدو مرتبًا كل يوم عمل دون أن تفكر كثيرًا.',
                    'content' => <<<'HTML'
<p>أناقة العمل اليوم لا تعتمد على البدلة بقدر ما تعتمد على المقاس والخامة وبعض القواعد الثابتة.</p>
<h2>دورة القمصان</h2>
<p>امتلك خمسة قمصان ولن تكرر قميصًا في الأسبوع: اثنان أبيضان مثل {{crisp-white-dress-shirt|القميص الأبيض الرسمي}}، و{{sky-blue-oxford-shirt|قميص أكسفورد السماوي}}، و{{navy-button-up-shirt|القميص الكحلي}}، وقميص بخطوط هادئة.</p>
<h2>قائمة المقاس الصحيح</h2>
<ul>
<li>درزة الكتف تقع تمامًا على عظمة الكتف.</li>
<li>يدخل إصبعان داخل الياقة وهي مقفلة.</li>
<li>ينتهي الكم عند التقاء المعصم باليد.</li>
</ul>
<h2>اللمسات الأخيرة</h2>
<p>نسّق الحزام مع الحذاء، واجعل الساعة بسيطة، واختر عطرًا يبقى قريبًا من البشرة؛ فالمكتب ليس مكان العطور الصاخبة.</p>
<blockquote>قاعدة الخميس: استبدل القميص الرسمي بـ{{classic-pique-polo|بولو بيكيه}} وأبقِ كل شيء آخر كما هو.</blockquote>
HTML,
                ],
            ],
            [
                'slug' => 'leather-jacket-care-guide',
                'category' => 'care',
                'photo' => 'photo-1623854156816-4c4fc355ffc7',
                'featured' => false,
                'days_ago' => 13,
                'en' => [
                    'title' => 'A Leather Jacket for Ten Years: The Complete Care Guide',
                    'excerpt' => 'Good leather gets better with age, if you treat it right. Storage, cleaning and the two things you must never do.',
                    'content' => <<<'HTML'
<p>A leather jacket is one of the few pieces that looks better in year five than in week one. It only needs a little attention.</p>
<h2>Every wear</h2>
<ul>
<li>Hang it on a wide, shaped hanger, never a wire one.</li>
<li>Let it air for a few hours before closing the wardrobe.</li>
</ul>
<h2>Every season</h2>
<p>Wipe with a soft damp cloth, let it dry naturally, then apply a thin layer of leather conditioner. It keeps jackets like the {{brown-leather-jacket|brown leather jacket}} supple and prevents cracking.</p>
<h2>Never</h2>
<ol>
<li>Never dry it with a hairdryer or in direct sun.</li>
<li>Never store it in plastic; leather needs to breathe. Use a cotton cover.</li>
</ol>
<blockquote>Caught in the rain? Blot, do not rub, and dry at room temperature.</blockquote>
<p>Prefer a cleaner silhouette? The {{minimal-leather-jacket|minimal leather jacket}} follows the same care rules.</p>
HTML,
                ],
                'ar' => [
                    'title' => 'جاكيت جلد يعيش عشر سنوات: دليل العناية الكامل',
                    'excerpt' => 'الجلد الجيد يزداد جمالًا مع الوقت إذا عاملته بشكل صحيح. التخزين والتنظيف، وأمران لا تفعلهما أبدًا.',
                    'content' => <<<'HTML'
<p>الجاكيت الجلدي من القطع القليلة التي تبدو أجمل في عامها الخامس من أسبوعها الأول، بشرط القليل من الاهتمام.</p>
<h2>بعد كل استخدام</h2>
<ul>
<li>علّقه على شماعة عريضة ومنحنية، لا على شماعة سلكية.</li>
<li>اتركه يتهوى بضع ساعات قبل إغلاق الخزانة.</li>
</ul>
<h2>كل موسم</h2>
<p>امسحه بقطعة قماش ناعمة ورطبة، واتركه يجف طبيعيًا، ثم ضع طبقة رقيقة من مرطب الجلد. هذا يحافظ على ليونة جاكيتات مثل {{brown-leather-jacket|الجاكيت الجلدي البني}} ويمنع التشقق.</p>
<h2>لا تفعل أبدًا</h2>
<ol>
<li>لا تجففه بمجفف الشعر أو تحت الشمس المباشرة.</li>
<li>لا تخزنه في كيس بلاستيكي؛ فالجلد يحتاج إلى التنفس، استخدم غطاءً قطنيًا.</li>
</ol>
<blockquote>تعرّض للمطر؟ جففه بالتربيت لا بالفرك، واتركه في حرارة الغرفة.</blockquote>
<p>تفضّل قصّة أبسط؟ {{minimal-leather-jacket|الجاكيت الجلدي البسيط}} يحتاج العناية نفسها.</p>
HTML,
                ],
            ],
            [
                'slug' => 'a-bag-for-every-day-of-the-week',
                'category' => 'accessories',
                'photo' => 'photo-1598532163257-ae3c6b2524b6',
                'featured' => false,
                'days_ago' => 18,
                'en' => [
                    'title' => 'Seven Days, Seven Bags? You Only Need Four',
                    'excerpt' => 'From the Sunday meeting to the Friday dinner, the four bag shapes that cover every plan in your week.',
                    'content' => <<<'HTML'
<p>You do not need a bag for every outfit; you need a bag for every kind of day.</p>
<h2>1. The workhorse tote</h2>
<p>Laptop, documents, lunch. A structured tote like the {{cognac-leather-tote|cognac leather tote}} keeps its shape and looks professional.</p>
<h2>2. The hands-free crossbody</h2>
<p>For malls, errands and travel. The {{stone-crossbody-bag|stone crossbody}} sits close and leaves your hands free.</p>
<h2>3. The polished top-handle</h2>
<p>Meetings, lunches, family visits. The {{noir-structured-handbag|noir structured handbag}} instantly lifts a simple outfit.</p>
<h2>4. The evening mini</h2>
<p>Phone, card, lipstick, nothing more. The {{pearl-white-mini-purse|pearl white mini purse}} is made for weddings and dinners.</p>
<blockquote>Colour strategy: one neutral (black or cognac), one light (stone or white), one fun accent.</blockquote>
HTML,
                ],
                'ar' => [
                    'title' => 'سبعة أيام وسبع حقائب؟ تكفيك أربع',
                    'excerpt' => 'من اجتماع الأحد إلى عشاء الجمعة، أربعة أشكال من الحقائب تغطي كل خططك في الأسبوع.',
                    'content' => <<<'HTML'
<p>لا تحتاجين حقيبة لكل إطلالة، بل حقيبة لكل نوع من الأيام.</p>
<h2>١. حقيبة التوت العملية</h2>
<p>اللابتوب والأوراق والغداء. الحقيبة المتماسكة مثل {{cognac-leather-tote|حقيبة التوت الجلدية بلون الكونياك}} تحافظ على شكلها وتبدو احترافية.</p>
<h2>٢. حقيبة الكروس لليدين الحرتين</h2>
<p>للمولات والمشاوير والسفر. {{stone-crossbody-bag|حقيبة الكروس الحجرية}} تبقى قريبة منكِ وتترك يديكِ حرتين.</p>
<h2>٣. الحقيبة الأنيقة بمقبض علوي</h2>
<p>للاجتماعات والغداء والزيارات العائلية. {{noir-structured-handbag|الحقيبة السوداء المتماسكة}} ترفع مستوى أبسط إطلالة.</p>
<h2>٤. الحقيبة الصغيرة للمساء</h2>
<p>الجوال والبطاقة وأحمر الشفاه فقط. {{pearl-white-mini-purse|الحقيبة الصغيرة اللؤلؤية}} مصممة للأعراس والعشاء.</p>
<blockquote>استراتيجية الألوان: لون محايد (أسود أو كونياك)، ولون فاتح (حجري أو أبيض)، ولون مرح واحد.</blockquote>
HTML,
                ],
            ],
            [
                'slug' => 'white-sneakers-keep-them-clean',
                'category' => 'care',
                'photo' => 'photo-1600269452121-4f2416e55c28',
                'featured' => false,
                'days_ago' => 24,
                'en' => [
                    'title' => 'White Sneakers: Keep Them Bright, Wear Them With Everything',
                    'excerpt' => 'The most versatile shoe you will own, and five honest ways to keep it looking new.',
                    'content' => <<<'HTML'
<p>White sneakers go with jeans, chinos, dresses and even tailoring. The only catch is keeping them white.</p>
<h2>Five habits that work</h2>
<ol>
<li><strong>Protect on day one:</strong> a water and stain repellent spray before the first wear.</li>
<li><strong>Wipe weekly:</strong> a damp cloth and a drop of mild soap on leather pairs like the {{white-leather-trainers|white leather trainers}}.</li>
<li><strong>Clean the soles:</strong> an old toothbrush and baking soda paste.</li>
<li><strong>Wash the laces separately:</strong> they yellow first.</li>
<li><strong>Rest them:</strong> rotate with another pair so they dry out completely.</li>
</ol>
<h2>How to wear them</h2>
<p>With a slip dress for contrast, with straight jeans for a classic look, or with a navy suit on a relaxed Thursday. {{perforated-white-sneakers|Perforated pairs}} stay cooler in summer.</p>
<blockquote>Never put leather sneakers in the washing machine; it ruins their shape.</blockquote>
HTML,
                ],
                'ar' => [
                    'title' => 'الحذاء الرياضي الأبيض: حافظ على لونه وارتده مع كل شيء',
                    'excerpt' => 'أكثر حذاء متعدد الاستخدامات ستملكه، وخمس طرق صادقة لإبقائه كالجديد.',
                    'content' => <<<'HTML'
<p>الحذاء الأبيض يناسب الجينز والتشينو والفساتين وحتى البدلات. التحدي الوحيد هو إبقاؤه أبيض.</p>
<h2>خمس عادات تنجح فعلًا</h2>
<ol>
<li><strong>احمِه من اليوم الأول:</strong> بخاخ مقاوم للماء والبقع قبل أول استخدام.</li>
<li><strong>امسحه أسبوعيًا:</strong> قطعة قماش رطبة ونقطة صابون لطيف للأحذية الجلدية مثل {{white-leather-trainers|الحذاء الجلدي الأبيض}}.</li>
<li><strong>نظّف النعل:</strong> فرشاة أسنان قديمة وعجينة بيكربونات الصوديوم.</li>
<li><strong>اغسل الأربطة وحدها:</strong> فهي أول ما يصفر.</li>
<li><strong>أرِحه:</strong> بدّله مع زوج آخر ليجف تمامًا.</li>
</ol>
<h2>كيف ترتديه</h2>
<p>مع فستان ساتان لتباين جميل، ومع جينز مستقيم لإطلالة كلاسيكية، أو مع بدلة كحلية في خميس هادئ. و{{perforated-white-sneakers|الحذاء المثقّب}} أبرد في الصيف.</p>
<blockquote>لا تضع الأحذية الجلدية في الغسالة أبدًا؛ فهي تفسد شكلها.</blockquote>
HTML,
                ],
            ],
        ];
    }

    private static function part4(): array
    {
        return [
            [
                'slug' => 'how-to-layer-necklaces',
                'category' => 'accessories',
                'photo' => 'photo-1599643478518-a784e5dc4c8f',
                'featured' => false,
                'days_ago' => 10,
                'en' => [
                    'title' => 'The Layered Look: Wearing Several Necklaces Without the Clutter',
                    'excerpt' => 'Three lengths, one metal, one hero. The simple formula behind every effortless jewellery stack.',
                    'content' => <<<'HTML'
<p>Layered necklaces look effortless, but the best stacks follow a quiet formula.</p>
<h2>The 3-2-1 formula</h2>
<ul>
<li><strong>3 lengths</strong>, roughly 5 cm apart: choker, collarbone, chest.</li>
<li><strong>2 textures</strong>: one fine chain, one with presence.</li>
<li><strong>1 hero</strong> pendant that draws the eye.</li>
</ul>
<h2>A stack we love</h2>
<p>Start with the {{delicate-circle-necklace|delicate circle necklace}} high on the neck, add the {{gold-bar-pendant|gold bar pendant}} at the collarbone, and let the {{sapphire-layered-necklace|sapphire layered necklace}} be the hero.</p>
<h2>Neckline matters</h2>
<p>V-necks love long pendants, crew necks prefer shorter chains, and an open collar shirt is the perfect frame for a full stack.</p>
<blockquote>Tangles? Fasten each chain, then hold them apart for three seconds before letting go.</blockquote>
HTML,
                ],
                'ar' => [
                    'title' => 'إطلالة الطبقات: كيف ترتدين أكثر من سلسلة دون ازدحام',
                    'excerpt' => 'ثلاثة أطوال ومعدن واحد وقطعة بطلة. المعادلة البسيطة وراء كل تنسيق مجوهرات أنيق.',
                    'content' => <<<'HTML'
<p>تبدو السلاسل المتعددة عفوية، لكن أجمل التنسيقات تتبع معادلة هادئة.</p>
<h2>معادلة ٣-٢-١</h2>
<ul>
<li><strong>ثلاثة أطوال</strong> بينها نحو ٥ سم: قريبة من الرقبة، وعند الترقوة، وعلى الصدر.</li>
<li><strong>خامتان:</strong> سلسلة ناعمة وأخرى بحضور أوضح.</li>
<li><strong>قطعة بطلة واحدة</strong> تجذب النظر.</li>
</ul>
<h2>تنسيق نحبه</h2>
<p>ابدئي بـ{{delicate-circle-necklace|سلسلة الدائرة الرقيقة}} قريبة من الرقبة، ثم {{gold-bar-pendant|قلادة القضيب الذهبي}} عند الترقوة، واجعلي {{sapphire-layered-necklace|قلادة الياقوت المتعددة}} البطلة.</p>
<h2>فتحة الرقبة تصنع الفرق</h2>
<p>فتحة V تحب القلادات الطويلة، والرقبة الدائرية تفضل السلاسل القصيرة، والقميص بياقة مفتوحة إطار مثالي لتنسيق كامل.</p>
<blockquote>تتشابك السلاسل؟ أغلقي كل سلسلة ثم أبعديها عن بعضها ثلاث ثوانٍ قبل أن تتركيها.</blockquote>
HTML,
                ],
            ],
            [
                'slug' => 'eid-and-wedding-season-outfits',
                'category' => 'occasions',
                'photo' => 'photo-1494578379344-d6c710782a3d',
                'featured' => true,
                'days_ago' => 2,
                'en' => [
                    'title' => 'Eid, Weddings and Gatherings: Your Occasion-Ready Checklist',
                    'excerpt' => 'The season of invitations is here. Plan your looks, avoid last-minute stress and arrive feeling your best.',
                    'content' => <<<'HTML'
<p>Between Eid visits, weddings and family dinners, the calendar fills fast. A little planning turns stress into excitement.</p>
<h2>Two weeks before</h2>
<ul>
<li>List every event and its dress code.</li>
<li>Try on what you own; note what needs tailoring.</li>
<li>Order new pieces early. Our delivery reaches every city in the Kingdom, but the season is busy.</li>
</ul>
<h2>Looks that work</h2>
<p>For evenings, a flowing {{blush-pink-evening-dress|blush evening dress}} with {{silver-peep-toe-heels|silver heels}} and a {{pearl-white-mini-purse|pearl mini purse}}. For daytime visits, a {{floral-halter-summer-dress|floral dress}} feels festive yet relaxed.</p>
<p>For men: a {{crisp-white-dress-shirt|crisp white shirt}}, a {{two-tone-dress-watch|dress watch}} and a warm fragrance such as {{golden-saffron-parfum|Golden Saffron}}.</p>
<h2>The day itself</h2>
<blockquote>Lay everything out the night before: outfit, shoes, bag, jewellery, fragrance. Your future self will thank you.</blockquote>
HTML,
                ],
                'ar' => [
                    'title' => 'العيد والأعراس والتجمعات: قائمتك لتكوني جاهزة لكل مناسبة',
                    'excerpt' => 'موسم الدعوات بدأ. خططي لإطلالاتك، وتجنبي توتر اللحظة الأخيرة، واحضري وأنتِ في أجمل حالاتك.',
                    'content' => <<<'HTML'
<p>بين زيارات العيد والأعراس وعزائم العائلة، يمتلئ التقويم بسرعة. قليل من التخطيط يحوّل التوتر إلى حماس.</p>
<h2>قبل أسبوعين</h2>
<ul>
<li>اكتبي كل مناسبة وطبيعة لبسها.</li>
<li>جرّبي ما لديكِ، ودوّني ما يحتاج تعديلًا عند الخياط.</li>
<li>اطلبي القطع الجديدة مبكرًا؛ توصيلنا يصل لكل مدن المملكة، لكن الموسم مزدحم.</li>
</ul>
<h2>إطلالات تنجح دائمًا</h2>
<p>للسهرة: {{blush-pink-evening-dress|فستان السهرة الوردي}} مع {{silver-peep-toe-heels|الكعب الفضي}} و{{pearl-white-mini-purse|الحقيبة اللؤلؤية الصغيرة}}. وللزيارات النهارية يمنحكِ {{floral-halter-summer-dress|الفستان المورّد}} إحساسًا احتفاليًا ومريحًا.</p>
<p>وللرجال: {{crisp-white-dress-shirt|قميص أبيض أنيق}}، و{{two-tone-dress-watch|ساعة رسمية}}، وعطر دافئ مثل {{golden-saffron-parfum|الزعفران الذهبي}}.</p>
<h2>في يوم المناسبة</h2>
<blockquote>جهّزي كل شيء من الليلة السابقة: الإطلالة والحذاء والحقيبة والمجوهرات والعطر. ستشكرين نفسكِ في الصباح.</blockquote>
HTML,
                ],
            ],
            [
                'slug' => 'the-hoodie-guide-comfort-meets-style',
                'category' => 'mens-style',
                'photo' => 'photo-1578768079052-aa76e52ff62e',
                'featured' => false,
                'days_ago' => 27,
                'en' => [
                    'title' => 'The Grown-Up Hoodie: Comfort That Still Looks Sharp',
                    'excerpt' => 'Desert nights, airport days and lazy weekends. How to choose and style a hoodie that does not look like gym wear.',
                    'content' => <<<'HTML'
<p>Riyadh winter nights, long flights, weekend errands: the hoodie earns its place. The difference between sloppy and stylish is all in the details.</p>
<h2>Choose well</h2>
<ul>
<li><strong>Weight:</strong> heavier cotton holds its shape and drapes better.</li>
<li><strong>Colour:</strong> earthy tones like {{mocha-oversized-hoodie|mocha}} and {{off-white-fleece-hoodie|off-white}} look more refined than bright logos.</li>
<li><strong>Fit:</strong> oversized is fine; just keep the trousers slimmer for balance.</li>
</ul>
<h2>Three ways to wear it</h2>
<ol>
<li>Under a {{black-leather-biker-jacket|leather biker jacket}} with dark jeans.</li>
<li>With straight jeans and {{white-leather-trainers|white trainers}} for travel.</li>
<li>Layered over a {{essential-white-tee|white tee}} with the hem just showing.</li>
</ol>
HTML,
                ],
                'ar' => [
                    'title' => 'الهودي بأسلوب ناضج: راحة تبدو أنيقة',
                    'excerpt' => 'ليالي البر وأيام المطار وعطلات الكسل. كيف تختار هوديًا وتنسقه دون أن يبدو كملابس النادي.',
                    'content' => <<<'HTML'
<p>ليالي الشتاء في الرياض، والرحلات الطويلة، ومشاوير نهاية الأسبوع؛ الهودي يستحق مكانه. والفرق بين الإهمال والأناقة في التفاصيل.</p>
<h2>اختر بذكاء</h2>
<ul>
<li><strong>الوزن:</strong> القطن الثقيل يحافظ على شكله وينسدل أجمل.</li>
<li><strong>اللون:</strong> الألوان الترابية مثل {{mocha-oversized-hoodie|الموكا}} و{{off-white-fleece-hoodie|الأوف وايت}} أرقى من الشعارات الصاخبة.</li>
<li><strong>المقاس:</strong> الواسع جميل، لكن اجعل البنطال أضيق قليلًا للتوازن.</li>
</ul>
<h2>ثلاث طرق لارتدائه</h2>
<ol>
<li>تحت {{black-leather-biker-jacket|جاكيت جلد بايكر}} مع جينز داكن.</li>
<li>مع جينز مستقيم و{{white-leather-trainers|حذاء أبيض}} للسفر.</li>
<li>فوق {{essential-white-tee|تيشيرت أبيض}} مع ظهور طرفه السفلي.</li>
</ol>
HTML,
                ],
            ],
            [
                'slug' => 'find-your-perfect-jeans',
                'category' => 'style-guide',
                'photo' => 'photo-1542272604-787c3835535d',
                'featured' => false,
                'days_ago' => 31,
                'en' => [
                    'title' => 'Slim, Straight or Relaxed? Finding Your Perfect Jeans',
                    'excerpt' => 'Denim is personal. A clear guide to cuts, washes and the fit test you can do in any mirror.',
                    'content' => <<<'HTML'
<p>The right pair of jeans makes everything else in your wardrobe easier. Here is how to find it.</p>
<h2>Cuts at a glance</h2>
<table>
<thead><tr><th>Cut</th><th>Best for</th><th>Pair with</th></tr></thead>
<tbody>
<tr><td>{{slim-fit-denim|Slim}}</td><td>A clean, modern line</td><td>Shirts, leather jackets</td></tr>
<tr><td>{{straight-fit-blue-jeans|Straight}}</td><td>Every body, every day</td><td>Tees, polos, trainers</td></tr>
<tr><td>{{light-wash-relaxed-jeans|Relaxed}}</td><td>Comfort and a 90s feel</td><td>Hoodies, boxy tees</td></tr>
</tbody>
</table>
<h2>Washes</h2>
<p>Dark wash dresses up, mid wash is the all-rounder, light wash is casual and summery.</p>
<h2>The mirror test</h2>
<ul>
<li>Waistband stays put when you sit, without a gap at the back.</li>
<li>No pulling lines across the thighs.</li>
<li>The hem breaks once on your shoe, or ends just above it for a cropped look.</li>
</ul>
<blockquote>Wash denim inside out in cold water, and as rarely as you can.</blockquote>
HTML,
                ],
                'ar' => [
                    'title' => 'سليم أم مستقيم أم واسع؟ اعثر على الجينز المثالي',
                    'excerpt' => 'الجينز اختيار شخصي. دليل واضح للقصات ودرجات الغسيل، واختبار المرآة الذي تجريه في أي مكان.',
                    'content' => <<<'HTML'
<p>الجينز المناسب يجعل بقية خزانتك أسهل. إليك كيف تجده.</p>
<h2>القصات في لمحة</h2>
<table>
<thead><tr><th>القصة</th><th>الأنسب لـ</th><th>نسّقه مع</th></tr></thead>
<tbody>
<tr><td>{{slim-fit-denim|سليم}}</td><td>خط عصري ونظيف</td><td>القمصان والجاكيتات الجلدية</td></tr>
<tr><td>{{straight-fit-blue-jeans|مستقيم}}</td><td>كل الأجسام وكل الأيام</td><td>التيشيرتات والبولو والأحذية الرياضية</td></tr>
<tr><td>{{light-wash-relaxed-jeans|واسع}}</td><td>الراحة وروح التسعينيات</td><td>الهوديز والتيشيرتات الواسعة</td></tr>
</tbody>
</table>
<h2>درجات الغسيل</h2>
<p>الداكن أكثر رسمية، والمتوسط يناسب كل شيء، والفاتح كاجوال وصيفي.</p>
<h2>اختبار المرآة</h2>
<ul>
<li>الخصر ثابت عند الجلوس دون فراغ من الخلف.</li>
<li>لا خطوط شد على الفخذين.</li>
<li>الطرف يلامس الحذاء مرة واحدة، أو ينتهي فوقه بقليل لإطلالة قصيرة.</li>
</ul>
<blockquote>اغسل الجينز مقلوبًا بماء بارد، وبأقل عدد مرات ممكن.</blockquote>
HTML,
                ],
            ],
        ];
    }
}
