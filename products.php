<?php
// The shop's menu. Prices in satang (THB x 100) to avoid rounding errors.
// All set to 100 (฿1.00) for safe live testing.
return [
    'americano' => [
        'cat' => 'coffee', 'emoji' => '☕', 'price' => 100,
        'name' => ['en' => 'Hot Americano', 'th' => 'อเมริกาโน่ร้อน'],
        'desc' => ['en' => 'Double shot over hot water, house blend', 'th' => 'เอสเพรสโซ่ช็อตคู่ เมล็ดเบลนด์ของร้าน'],
    ],
    'iced_latte' => [
        'cat' => 'coffee', 'emoji' => '🥛', 'price' => 100,
        'name' => ['en' => 'Iced Latte', 'th' => 'ลาเต้เย็น'],
        'desc' => ['en' => 'Espresso with fresh milk over ice', 'th' => 'เอสเพรสโซ่ผสมนมสด เสิร์ฟพร้อมน้ำแข็ง'],
    ],
    'thai_coffee' => [
        'cat' => 'coffee', 'emoji' => '🧋', 'price' => 100,
        'name' => ['en' => 'Thai Iced Coffee', 'th' => 'กาแฟเย็นสูตรโบราณ'],
        'desc' => ['en' => 'Old-style Thai coffee with condensed milk', 'th' => 'กาแฟโบราณหอมเข้ม ผสมนมข้นหวาน'],
    ],
    'thai_tea' => [
        'cat' => 'tea', 'emoji' => '🥤', 'price' => 100,
        'name' => ['en' => 'Thai Iced Tea', 'th' => 'ชาไทยเย็น'],
        'desc' => ['en' => 'Rich Thai tea with condensed milk', 'th' => 'ชาไทยรสเข้มข้น ผสมนมข้นหวาน'],
    ],
    'matcha' => [
        'cat' => 'tea', 'emoji' => '🍵', 'price' => 100,
        'name' => ['en' => 'Iced Matcha Latte', 'th' => 'มัทฉะลาเต้เย็น'],
        'desc' => ['en' => 'Japanese matcha with fresh milk', 'th' => 'มัทฉะญี่ปุ่นแท้ ผสมนมสด'],
    ],
    'lemon_tea' => [
        'cat' => 'tea', 'emoji' => '🍋', 'price' => 100,
        'name' => ['en' => 'Iced Lemon Tea', 'th' => 'ชามะนาว'],
        'desc' => ['en' => 'Black tea, fresh lime and honey', 'th' => 'ชาดำ มะนาวสด และน้ำผึ้ง'],
    ],
    'croissant' => [
        'cat' => 'bakery', 'emoji' => '🥐', 'price' => 100,
        'name' => ['en' => 'Butter Croissant', 'th' => 'ครัวซองต์เนยสด'],
        'desc' => ['en' => 'Baked fresh every morning', 'th' => 'อบสดใหม่ทุกเช้า'],
    ],
    'mango' => [
        'cat' => 'bakery', 'emoji' => '🥭', 'price' => 100,
        'name' => ['en' => 'Mango Sticky Rice', 'th' => 'ข้าวเหนียวมะม่วง'],
        'desc' => ['en' => 'Nam Dok Mai mango with coconut cream', 'th' => 'มะม่วงน้ำดอกไม้ ราดกะทิสด'],
    ],
    'coconut_cake' => [
        'cat' => 'bakery', 'emoji' => '🍰', 'price' => 100,
        'name' => ['en' => 'Coconut Cake', 'th' => 'เค้กมะพร้าวอ่อน'],
        'desc' => ['en' => 'Soft sponge with young coconut', 'th' => 'เนื้อเค้กนุ่ม ใส่มะพร้าวอ่อน'],
    ],
];
