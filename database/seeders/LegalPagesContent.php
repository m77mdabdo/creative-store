<?php

namespace Database\Seeders;

/**
 * Texts for LegalPagesSeeder. Placeholders in [brackets] must be filled in.
 */
final class LegalPagesContent
{
    public static function pages(): array
    {
        return [
            'privacy' => self::privacy(),
            'terms'   => self::terms(),
            'returns' => self::returns(),
            'shipping-policy' => self::shipping(),
            'faq'     => self::faq(),
        ];
    }

    private static function privacy(): array
    {
        return [
            'ar' => ['سياسة الخصوصية', <<<'TXT'
نحترم خصوصيتك ونلتزم بحماية بياناتك الشخصية وفق نظام حماية البيانات الشخصية في المملكة العربية السعودية.

البيانات التي نجمعها
- الاسم والبريد الإلكتروني ورقم الجوال عند إنشاء الحساب.
- عنوان الشحن لتوصيل طلباتك.
- سجل الطلبات والمدفوعات والمراسلات مع خدمة العملاء.

كيف نستخدم بياناتك
- تنفيذ طلباتك وتوصيلها والتواصل معك بخصوصها.
- إرسال رموز التحقق وإشعارات الطلب.
- إرسال العروض فقط إذا اشتركت في النشرة البريدية، ويمكنك إلغاء الاشتراك في أي وقت.

الدفع
تتم معالجة الدفع عبر بوابة ميسر المرخّصة، ولا نقوم بتخزين بيانات بطاقتك على خوادمنا.

مشاركة البيانات
لا نبيع بياناتك. نشاركها فقط مع شركات الشحن وبوابة الدفع بالقدر اللازم لتنفيذ طلبك، أو مع الجهات الرسمية عند الطلب النظامي.

حقوقك
يحق لك طلب نسخة من بياناتك أو تصحيحها أو حذف حسابك بالتواصل معنا على [البريد الإلكتروني للدعم].

آخر تحديث: [التاريخ]
TXT],
            'en' => ['Privacy Policy', <<<'TXT'
We respect your privacy and protect your personal data in line with the Saudi Personal Data Protection Law.

What we collect
- Your name, email and mobile number when you create an account.
- Your delivery address to ship your orders.
- Your order, payment and support history.

How we use it
- To process, deliver and update you about your orders.
- To send verification codes and order notifications.
- To send offers only if you subscribe to our newsletter; you can unsubscribe at any time.

Payments
Payments are processed by Moyasar, a licensed payment gateway. We never store your card details on our servers.

Sharing
We do not sell your data. We share it only with delivery partners and the payment gateway as needed to fulfil your order, or with authorities when legally required.

Your rights
You can request a copy of your data, correct it or delete your account by contacting [support email].

Last updated: [date]
TXT],
        ];
    }

    private static function terms(): array
    {
        return [
            'ar' => ['الشروط والأحكام', <<<'TXT'
باستخدامك متجر كرييتف ستور فإنك توافق على الشروط التالية.

بيانات المتجر
الاسم التجاري: [الاسم التجاري] — السجل التجاري: [رقم السجل] — الرقم الضريبي: [الرقم الضريبي].

الأسعار والضريبة
الأسعار بالريال السعودي، وتُضاف ضريبة القيمة المضافة 15% وتظهر بوضوح قبل الدفع. قد تتغير الأسعار دون إشعار، ويُعتمد السعر الظاهر وقت إتمام الطلب.

الطلبات
يُعد الطلب مؤكدًا بعد نجاح الدفع ووصول رسالة التأكيد. يحق لنا إلغاء الطلب واسترداد المبلغ كاملًا إذا تعذر توفير المنتج.

الحساب
أنت مسؤول عن سرية بيانات دخولك وعن الطلبات التي تتم من حسابك.

الإلغاء والاسترجاع
يمكنك إلغاء الطلب قبل شحنه، والاسترجاع وفق سياسة الاسترجاع المنشورة في المتجر.

القانون المطبق
تخضع هذه الشروط لأنظمة المملكة العربية السعودية، ومنها نظام التجارة الإلكترونية ونظام حماية المستهلك.

للتواصل: [البريد الإلكتروني للدعم] — [رقم الجوال].
TXT],
            'en' => ['Terms and Conditions', <<<'TXT'
By using Creative Store you agree to the following terms.

Store details
Trade name: [trade name] — Commercial registration: [CR number] — VAT number: [VAT number].

Prices and VAT
Prices are in Saudi riyals. 15% VAT is added and shown clearly before payment. Prices may change without notice; the price shown when you place the order applies.

Orders
An order is confirmed once payment succeeds and you receive a confirmation. If an item becomes unavailable we may cancel the order and refund it in full.

Your account
You are responsible for keeping your login details safe and for orders placed from your account.

Cancellations and returns
You can cancel an order before it ships, and return items under our published returns policy.

Governing law
These terms are governed by the laws of the Kingdom of Saudi Arabia, including the E-Commerce Law and the Consumer Protection Law.

Contact: [support email] — [phone].
TXT],
        ];
    }

    private static function returns(): array
    {
        return [
            'ar' => ['سياسة الاسترجاع والاستبدال', <<<'TXT'
يحق لك إرجاع المنتج خلال 7 أيام من تاريخ استلامه، وفق نظام التجارة الإلكترونية في المملكة.

شروط الاسترجاع
- أن يكون المنتج بحالته الأصلية وغير مستخدم ومعه الملصقات والتغليف.
- إرفاق رقم الطلب.

منتجات لا يمكن إرجاعها
- العطور المفتوحة، والمجوهرات والإكسسوارات الشخصية بعد الاستخدام، لأسباب صحية.
- المنتجات المصنوعة حسب الطلب.

المنتجات التالفة أو الخاطئة
إذا وصلك منتج تالف أو مختلف عن طلبك نتحمل تكلفة الشحن ونستبدله أو نرد المبلغ كاملًا.

الاسترداد
يُرد المبلغ لنفس وسيلة الدفع خلال 5 إلى 14 يوم عمل من استلامنا للمنتج، حسب البنك المصدر للبطاقة.

لبدء طلب استرجاع تواصل معنا على [البريد الإلكتروني للدعم] أو من صفحة "تواصل معنا".
TXT],
            'en' => ['Returns and Exchanges', <<<'TXT'
You may return an item within 7 days of receiving it, in line with the Saudi E-Commerce Law.

Conditions
- The item is unused, in its original condition, with tags and packaging.
- Include your order number.

Items that cannot be returned
- Opened fragrances, and worn jewelry or personal accessories, for hygiene reasons.
- Made-to-order items.

Damaged or incorrect items
If an item arrives damaged or is not what you ordered, we cover shipping and replace it or refund you in full.

Refunds
Refunds go back to the original payment method within 5 to 14 business days of us receiving the item, depending on your card issuer.

To start a return, contact [support email] or use the Contact page.
TXT],
        ];
    }

    private static function shipping(): array
    {
        return [
            'ar' => ['سياسة الشحن والتوصيل', <<<'TXT'
نوصّل لجميع مدن المملكة العربية السعودية.

طرق التوصيل
- التوصيل العادي: من 1 إلى 6 أيام عمل حسب المدينة.
- التوصيل السريع: من يوم إلى 3 أيام عمل.
- التوصيل في نفس اليوم: داخل الرياض للطلبات قبل الساعة [الوقت].
- الاستلام من المتجر: [عنوان المتجر].

الرسوم
تظهر رسوم الشحن حسب مدينتك وطريقة التوصيل قبل الدفع، والشحن مجاني للطلبات التي تتجاوز الحد المعلن.

تتبع الطلب
بعد الشحن يصلك رقم التتبع، ويمكنك متابعة طلبك من صفحة "تتبع الطلب".

ملاحظات
تحسب أيام العمل من الأحد إلى الخميس، وقد تتأخر الطلبات في المواسم والعطلات الرسمية.
TXT],
            'en' => ['Shipping Policy', <<<'TXT'
We deliver to every city in Saudi Arabia.

Delivery options
- Standard: 1 to 6 business days depending on the city.
- Express: 1 to 3 business days.
- Same day: within Riyadh for orders placed before [time].
- Store pickup: [store address].

Fees
Shipping fees for your city and method are shown before payment. Orders above the advertised threshold ship free.

Tracking
Once your order ships you receive a tracking number and can follow it on the Track Order page.

Notes
Business days are Sunday to Thursday. Deliveries may take longer during peak seasons and public holidays.
TXT],
        ];
    }

    private static function faq(): array
    {
        return [
            'ar' => ['الأسئلة الشائعة', <<<'TXT'
كيف أعرف مقاسي؟
المقاسات المتاحة موضحة في صفحة كل منتج، ولو احتجت قياسات دقيقة تواصل معنا وسنساعدك. وإذا كنت بين مقاسين ننصح بالأكبر.

ما طرق الدفع المتاحة؟
بطاقات مدى وفيزا وماستركارد عبر بوابة ميسر الآمنة.

هل الأسعار تشمل الضريبة؟
تُضاف ضريبة القيمة المضافة 15% وتظهر في ملخص الطلب قبل الدفع.

هل يمكنني إلغاء طلبي؟
نعم، من صفحة "طلباتي" قبل شحن الطلب، ويُسترد المبلغ تلقائيًا.

كم تستغرق إعادة المبلغ؟
من 5 إلى 14 يوم عمل حسب البنك.

كيف أتواصل معكم؟
من صفحة "تواصل معنا" أو على [البريد الإلكتروني للدعم].
TXT],
            'en' => ['Frequently Asked Questions', <<<'TXT'
How do I find my size?
Available sizes are listed on each product page. Contact us for exact measurements. If you are between sizes, we recommend the larger one.

Which payment methods do you accept?
Mada, Visa and Mastercard through the secure Moyasar gateway.

Do prices include VAT?
15% VAT is added and shown in the order summary before you pay.

Can I cancel my order?
Yes, from My Orders before the order ships. The refund is issued automatically.

How long do refunds take?
5 to 14 business days depending on your bank.

How can I reach you?
Through the Contact page or at [support email].
TXT],
        ];
    }
}
