<?php

namespace Database\Seeders;

/**
 * Product list for DemoCatalogSeeder.
 *
 * Each row: [category, Unsplash photo id, English name, Arabic name, price in SAR, colours].
 */
final class DemoCatalogData
{
    public static function products(): array
    {
        return [
            ['women', 'photo-1568251188392-ae32f898cb3b', 'Classic Black Midi Dress', 'فستان ميدي أسود كلاسيكي', 349, ['Black']],
            ['women', 'photo-1494578379344-d6c710782a3d', 'Blush Pink Evening Dress', 'فستان سهرة وردي', 589, ['Pink']],
            ['women', 'photo-1499939667766-4afceb292d05', 'Floral Halter Summer Dress', 'فستان صيفي مورد برقبة هالتر', 279, ['Pink', 'White']],
            ['women', 'photo-1567966456076-905a50a06d8c', 'Beige V-Neck Knit Top', 'بلوزة تريكو بيج بفتحة V', 159, ['Beige', 'White']],
            ['women', 'photo-1563540153332-29de4b355f49', 'Cable Knit Cream Sweater', 'بلوفر تريكو كريمي مضفر', 229, ['White', 'Beige']],
            ['women', 'photo-1789110853872-f416085557fa', 'Terracotta Tie-Waist Shirt Dress', 'فستان قميص تيراكوتا بحزام', 319, ['Orange']],
            ['women', 'photo-1789111161016-795db6925e99', 'Sunflower Button-Down Dress', 'فستان أصفر بأزرار', 299, ['Yellow']],
            ['women', 'photo-1789110853844-d1a86b2c5b9b', 'Emerald Sleeveless Dress', 'فستان زمردي بدون أكمام', 339, ['Green']],
            ['women', 'photo-1789110853555-6a2d8ed60d57', 'Polka Dot Wrap Dress', 'فستان منقط بلفة', 289, ['Black']],
            ['women', 'photo-1789110520279-49e8a3a6fb91', 'Lavender Knit Cardigan', 'كارديجان تريكو لافندر', 199, ['Lavender']],
            ['women', 'photo-1760287363707-851f4780b98c', 'Teal Lace-Trim Dress', 'فستان تركوازي بحواف دانتيل', 419, ['Teal']],
            ['women', 'photo-1598554747436-c9293d6a588f', 'High-Rise Mom Jeans', 'جينز موم بخصر عالٍ', 249, ['Blue']],
            ['women', 'photo-1789110519342-6d3f6b07a749', 'Beige Slip Dress', 'فستان بيج ناعم بحمالات', 269, ['Beige']],
            ['women', 'photo-1789110520185-e1a6a694b795', 'Red Floral Tunic', 'تونيك أحمر مورد', 219, ['Red']],

            ['men', 'photo-1740711152088-88a009e877bb', 'Sky Blue Oxford Shirt', 'قميص أكسفورد أزرق سماوي', 189, ['Blue']],
            ['men', 'photo-1596755094514-f87e34085b2c', 'Patterned Casual Shirt', 'قميص كاجوال منقوش', 169, ['Blue']],
            ['men', 'photo-1602810318383-e386cc2a3ccf', 'Crisp White Dress Shirt', 'قميص رسمي أبيض', 199, ['White']],
            ['men', 'photo-1602810316693-3667c854239a', 'Navy Button-Up Shirt', 'قميص كحلي بأزرار', 189, ['Navy']],
            ['men', 'photo-1611312449412-6cefac5dc3e4', 'Grey Check Flannel Shirt', 'قميص فلانيل كاروهات رمادي', 179, ['Grey', 'Black']],
            ['men', 'photo-1603252110481-7ba873bf42ab', 'Lilac Pinstripe Shirt', 'قميص مقلم بنفسجي فاتح', 179, ['Purple', 'White']],
            ['men', 'photo-1623658580851-3b25bf83b4ea', 'Classic Pique Polo', 'بولو بيكيه كلاسيكي', 139, ['Blue', 'White', 'Black']],
            ['men', 'photo-1594938291221-94f18cbb5660', 'Striped Business Shirt', 'قميص عمل مقلم', 209, ['Blue']],
            ['men', 'photo-1581655353564-df123a1eb820', 'Essential White Tee', 'تيشيرت أبيض أساسي', 79, ['White']],
            ['men', 'photo-1618677603286-0ec56cb6e1b5', 'Heavyweight Cotton Tee', 'تيشيرت قطن ثقيل', 99, ['White', 'Black']],
            ['men', 'photo-1621951767587-b24334f11c65', 'Two-Tone Crew Neck Tee', 'تيشيرت برقبة دائرية بلونين', 89, ['Black', 'White']],
            ['men', 'photo-1759572095384-1a7e646d0d4f', 'Organic Cotton Tee Duo', 'طقم تيشيرتين قطن عضوي', 149, ['Black', 'Green']],
            ['men', 'photo-1602293589930-45aad59ba3ab', 'Straight Fit Blue Jeans', 'جينز أزرق قصة مستقيمة', 229, ['Blue']],
            ['men', 'photo-1542272604-787c3835535d', 'Slim Fit Denim', 'جينز سليم فت', 239, ['Blue', 'Black', 'Grey']],
            ['men', 'photo-1714143136372-ddaf8b606da7', 'Light Wash Relaxed Jeans', 'جينز فاتح واسع', 219, ['Blue']],

            ['outerwear', 'photo-1556821840-3a63f95609a7', 'Grey Pullover Hoodie', 'هودي رمادي', 189, ['Grey']],
            ['outerwear', 'photo-1579572331145-5e53b299c64e', 'Black Essential Hoodie', 'هودي أسود أساسي', 189, ['Black']],
            ['outerwear', 'photo-1620799140188-3b2a02fd9a77', 'Off-White Fleece Hoodie', 'هودي فليس أوف وايت', 209, ['White']],
            ['outerwear', 'photo-1578768079052-aa76e52ff62e', 'Mocha Oversized Hoodie', 'هودي واسع بني موكا', 219, ['Brown']],
            ['outerwear', 'photo-1609873814058-a8928924184a', 'Colour Block Hoodie', 'هودي بألوان متداخلة', 229, ['Yellow', 'Black']],
            ['outerwear', 'photo-1551028719-00167b16eac5', 'Black Leather Biker Jacket', 'جاكيت جلد أسود', 899, ['Black']],
            ['outerwear', 'photo-1623854156816-4c4fc355ffc7', 'Brown Leather Jacket', 'جاكيت جلد بني', 949, ['Brown']],
            ['outerwear', 'photo-1727515546577-f7d82a47b51d', 'Minimal Leather Jacket', 'جاكيت جلد بسيط', 799, ['Black']],

            ['shoes', 'photo-1595950653106-6c9ebd614d3a', 'Pastel Runner Sneakers', 'سنيكرز رياضي بألوان باستيل', 329, ['Pink', 'Blue']],
            ['shoes', 'photo-1525966222134-fcfa99b8ae77', 'Burgundy Canvas Plimsolls', 'حذاء قماش عنابي', 149, ['Red']],
            ['shoes', 'photo-1600269452121-4f2416e55c28', 'White Leather Trainers', 'سنيكرز جلد أبيض', 379, ['White']],
            ['shoes', 'photo-1608231387042-66d1773070a5', 'Perforated White Sneakers', 'سنيكرز أبيض مثقب', 349, ['White']],
            ['shoes', 'photo-1560769629-975ec94e6a86', 'Orange Accent Running Shoes', 'حذاء جري بلمسات برتقالية', 399, ['White', 'Orange']],
            ['shoes', 'photo-1600185365926-3a2ce3cdb9eb', 'Lightweight Performance Runner', 'حذاء جري خفيف', 429, ['White', 'Orange']],
            ['shoes', 'photo-1535043934128-cf0b28d52f95', 'Ivory Textured Pumps', 'كعب عاجي محبب', 359, ['White']],
            ['shoes', 'photo-1543163521-1bf539c55dd2', 'Floral Almond-Toe Pumps', 'كعب مورد بمقدمة لوزية', 389, ['Blue', 'Pink']],
            ['shoes', 'photo-1581101767113-1677fc2beaa8', 'Silver Peep-Toe Heels', 'كعب فضي مفتوح', 409, ['Silver']],
            ['shoes', 'photo-1518049362265-d5b2a6467637', 'Glitter Pointed Pumps', 'كعب لامع مدبب', 449, ['Silver', 'White']],
            ['shoes', 'photo-1590099033615-be195f8d575c', 'Grey Leather Sandals', 'صندل جلد رمادي', 279, ['Grey']],
            ['shoes', 'photo-1605733513549-de9b150bd70d', 'Black Leather Ankle Boots', 'بوت جلد أسود', 529, ['Black']],

            ['bags', 'photo-1598532163257-ae3c6b2524b6', 'Cognac Leather Tote', 'حقيبة توت جلد كونياك', 649, ['Brown']],
            ['bags', 'photo-1584917865442-de89df76afd3', 'Crimson Top-Handle Bag', 'حقيبة يد قرمزية', 699, ['Red']],
            ['bags', 'photo-1614179689702-355944cd0918', 'Noir Structured Handbag', 'حقيبة يد سوداء', 749, ['Black']],
            ['bags', 'photo-1600857062241-98e5dba7f214', 'Saddle Sling Bag', 'حقيبة كروس سرج', 459, ['Brown']],
            ['bags', 'photo-1682745230951-8a5aa9a474a0', 'Pearl White Mini Purse', 'شنطة صغيرة بيضاء لؤلؤية', 389, ['White']],
            ['bags', 'photo-1590874103328-eac38a683ce7', 'Heritage Leather Satchel', 'حقيبة ساتشل جلد', 579, ['Brown']],
            ['bags', 'photo-1605733513597-a8f8341084e6', 'Grey Buckle Satchel', 'حقيبة رمادية بأبازيم', 529, ['Grey']],
            ['bags', 'photo-1705909237050-7a7625b47fac', 'Black Everyday Shoulder Bag', 'حقيبة كتف سوداء يومية', 499, ['Black']],
            ['bags', 'photo-1559563458-527698bf5295', 'Stone Crossbody Bag', 'حقيبة كروس رمادية', 349, ['Grey']],
            ['bags', 'photo-1566150905458-1bf1fc113f0d', 'Blush Crossbody Bag', 'حقيبة كروس وردية', 349, ['Pink']],
            ['bags', 'photo-1591561954557-26941169b49e', 'Floral Print Handbag', 'حقيبة يد مطبوعة بالورود', 419, ['White', 'Red']],

            ['watches', 'photo-1523170335258-f5ed11844a49', 'Heritage Automatic Watch', 'ساعة أوتوماتيك كلاسيكية', 1290, ['Silver']],
            ['watches', 'photo-1620625515032-6ed0c1790c75', 'White Dial Steel Watch', 'ساعة ستيل بميناء أبيض', 890, ['Silver']],
            ['watches', 'photo-1524805444758-089113d48a6d', 'Steel Chronograph', 'ساعة كرونوغراف ستيل', 1150, ['Silver']],
            ['watches', 'photo-1622434641406-a158123450f9', 'Midnight Chronograph', 'ساعة كرونوغراف ليلية', 1190, ['Black', 'Silver']],
            ['watches', 'photo-1609587312208-cea54be969e7', 'Sport Chronograph', 'ساعة كرونوغراف رياضية', 990, ['Silver', 'Black']],
            ['watches', 'photo-1623998021661-dc7555b2213d', 'Two-Tone Dress Watch', 'ساعة رسمية بلونين', 1390, ['Gold', 'Silver']],
            ['watches', 'photo-1767009951316-c3d2b63179ef', 'Gold Dial Diver', 'ساعة غوص بميناء ذهبي', 1590, ['Gold']],
            ['watches', 'photo-1772638904164-eed7c6b1e57e', 'Obsidian Luxury Watch', 'ساعة فاخرة سوداء', 1890, ['Black']],

            ['sunglasses', 'photo-1577803645773-f96470509666', 'Ivory Frame Sunglasses', 'نظارة شمسية بإطار عاجي', 299, ['White']],
            ['sunglasses', 'photo-1584036553516-bf83210aa16c', 'Classic Black Square Sunglasses', 'نظارة مربعة سوداء', 279, ['Black']],
            ['sunglasses', 'photo-1508296695146-257a814070b4', 'Smoke Lens Clear Frame', 'نظارة بإطار شفاف وعدسات دخانية', 259, ['White', 'Grey']],
            ['sunglasses', 'photo-1559070081-648fb00b2ed1', 'Tortoiseshell Wayfarer', 'نظارة بإطار صدف السلحفاة', 319, ['Brown']],
            ['sunglasses', 'photo-1589642380614-4a8c2147b857', 'Silver Aviator', 'نظارة طيار فضية', 349, ['Silver']],
            ['sunglasses', 'photo-1599705709640-9f9eb5964485', 'Black Aviator', 'نظارة طيار سوداء', 349, ['Black']],

            ['jewelry', 'photo-1617038220319-276d3cfab638', 'Twisted Gold Hoops', 'حلق ذهبي ملتوي', 249, ['Gold']],
            ['jewelry', 'photo-1786052337650-9e1c0dd01f5e', 'Pearl Drop Necklace', 'قلادة بدلاية لؤلؤ', 459, ['Gold']],
            ['jewelry', 'photo-1589128777073-263566ae5e4d', 'Silver Heart Pendant', 'قلادة قلب فضية', 219, ['Silver']],
            ['jewelry', 'photo-1787520543189-8ce72b56ea19', 'Gold Bar Pendant', 'قلادة ذهبية بدلاية مستطيلة', 389, ['Gold']],
            ['jewelry', 'photo-1599643478518-a784e5dc4c8f', 'Sapphire Layered Necklace', 'قلادة طبقات بحجر أزرق', 529, ['Gold']],
            ['jewelry', 'photo-1773929345739-94db8a4ccd0c', 'Delicate Circle Necklace', 'قلادة دائرية ناعمة', 299, ['Gold']],
            ['jewelry', 'photo-1763256614589-199db7a3bd51', 'Rose Gold Crystal Pendant', 'قلادة روز جولد بكريستال', 399, ['Pink']],
            ['jewelry', 'photo-1602173574767-37ac01994b2a', 'Gold Link Bracelet', 'أسورة ذهبية بحلقات', 349, ['Gold']],

            ['fragrances', 'photo-1594035910387-fea47794261f', 'Oud Noir Eau de Parfum', 'عطر عود نوار', 459, []],
            ['fragrances', 'photo-1587017539504-67cfbddac569', 'Aqua Marine Cologne', 'كولونيا أكوا مارين', 299, []],
            ['fragrances', 'photo-1458538977777-0549b2370168', 'Pure Musk Eau de Toilette', 'عطر مسك صافي', 249, []],
            ['fragrances', 'photo-1615634260167-c8cdede054de', 'White Amber Parfum', 'عطر عنبر أبيض', 389, []],
            ['fragrances', 'photo-1585218334450-afcf929da36e', 'Golden Saffron Parfum', 'عطر زعفران ذهبي', 499, []],
            ['fragrances', 'photo-1760920250029-36af9369a0bb', 'Cedar and Vetiver', 'عطر أرز وفيتيفر', 359, []],
            ['fragrances', 'photo-1543422655-ac1c6ca993ed', 'Citrus Bloom', 'عطر زهر الحمضيات', 269, []],
            ['fragrances', 'photo-1622618991746-fe6004db3a47', 'Rose Petal Eau de Parfum', 'عطر بتلات الورد', 329, []],
            ['fragrances', 'photo-1761659760494-32b921a2449f', 'Silver Mist', 'عطر الضباب الفضي', 289, []],
        ];
    }
}
