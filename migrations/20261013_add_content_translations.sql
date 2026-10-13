ALTER TABLE packages
  ADD COLUMN name_bn VARCHAR(100) NULL,
  ADD COLUMN name_hi VARCHAR(100) NULL,
  ADD COLUMN line1_bn VARCHAR(100) NULL,
  ADD COLUMN line1_hi VARCHAR(100) NULL,
  ADD COLUMN line2_bn VARCHAR(100) NULL,
  ADD COLUMN line2_hi VARCHAR(100) NULL,
  ADD COLUMN line3_bn VARCHAR(100) NULL,
  ADD COLUMN line3_hi VARCHAR(100) NULL;

ALTER TABLE hero_banners
  ADD COLUMN title_bn VARCHAR(150) NULL,
  ADD COLUMN title_hi VARCHAR(150) NULL,
  ADD COLUMN subtitle_bn VARCHAR(200) NULL,
  ADD COLUMN subtitle_hi VARCHAR(200) NULL,
  ADD COLUMN description_bn TEXT NULL,
  ADD COLUMN description_hi TEXT NULL,
  ADD COLUMN button_text_bn VARCHAR(60) NULL,
  ADD COLUMN button_text_hi VARCHAR(60) NULL;

UPDATE packages SET
  name_bn = 'সাশ্রয়ী ওমরাহ',
  name_hi = 'किफायती उमराह',
  line1_bn = '৭ দিন / ৬ রাত',
  line1_hi = '7 दिन / 6 रात',
  line2_bn = '৩★ হোটেল (হারামের কাছে)',
  line2_hi = '3★ होटल (हरम के पास)',
  line3_bn = 'ভিসা সহায়তা',
  line3_hi = 'वीज़ा सहायता'
WHERE name = 'Economy Umrah';

UPDATE packages SET
  name_bn = 'স্ট্যান্ডার্ড ওমরাহ',
  name_hi = 'स्टैंडर्ड उमराह',
  line1_bn = '১০ দিন / ৯ রাত',
  line1_hi = '10 दिन / 9 रात',
  line2_bn = '৪★ হোটেল (হারামের কাছে)',
  line2_hi = '4★ होटल (हरम के पास)',
  line3_bn = 'পরিবহন অন্তর্ভুক্ত',
  line3_hi = 'परिवहन शामिल'
WHERE name = 'Standard Umrah';

UPDATE packages SET
  name_bn = 'প্রিমিয়াম ওমরাহ',
  name_hi = 'प्रीमियम उमराह',
  line1_bn = '১২ দিন / ১১ রাত',
  line1_hi = '12 दिन / 11 रात',
  line2_bn = '৫★ হোটেল (হারামের কাছে)',
  line2_hi = '5★ होटल (हरम के पास)',
  line3_bn = 'ভিআইপি পরিবহন',
  line3_hi = 'वीआईपी परिवहन'
WHERE name = 'Premium Umrah';

UPDATE packages SET
  name_bn = 'হজ প্যাকেজ',
  name_hi = 'हज पैकेज',
  line1_bn = '১৫ দিন / ১৪ রাত',
  line1_hi = '15 दिन / 14 रात',
  line2_bn = '৪★ হোটেল (হারামের কাছে)',
  line2_hi = '4★ होटल (हरम के पास)',
  line3_bn = 'সম্পূর্ণ গাইডেন্স',
  line3_hi = 'पूरा मार्गदर्शन'
WHERE name = 'Hajj Package';
