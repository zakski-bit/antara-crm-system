ALTER TABLE `email_messages`
    ADD COLUMN `category` VARCHAR(50) NOT NULL DEFAULT 'general' AFTER `has_attachments`,
    ADD COLUMN `labels` VARCHAR(255) NOT NULL DEFAULT '' AFTER `category`,
    ADD COLUMN `scheduled_for` DATETIME DEFAULT NULL AFTER `labels`;

CREATE INDEX IF NOT EXISTS `email_messages_category_index` ON `email_messages` (`category`);
CREATE INDEX IF NOT EXISTS `email_messages_scheduled_index` ON `email_messages` (`scheduled_for`);

UPDATE `email_messages`
SET
    `category` = 'events',
    `labels` = 'Team Events,Internal',
    `scheduled_for` = DATE_ADD(NOW(), INTERVAL 1 DAY)
WHERE `subject` = 'Pembaruan Jadwal Rapat Redaksi';

UPDATE `email_messages`
SET
    `category` = 'campaign',
    `labels` = 'Work,External',
    `scheduled_for` = NULL,
    `is_starred` = 1
WHERE `subject` = 'Brief Kampanye Konten Mitra Telkom';

UPDATE `email_messages`
SET
    `category` = 'compliance',
    `labels` = 'External',
    `scheduled_for` = NULL
WHERE `subject` = 'Reminder Akreditasi Liputan Istana';

UPDATE `email_messages`
SET
    `category` = 'campaign',
    `labels` = 'Projects',
    `scheduled_for` = NULL
WHERE `subject` = 'Distribusi Rilis Pers Kolaborasi Unesco';

UPDATE `email_messages`
SET
    `category` = 'work',
    `labels` = 'Work',
    `scheduled_for` = NULL
WHERE `subject` = 'Konsep Newsletter Investor Desember';

INSERT INTO `email_messages`
    (`folder`, `subject`, `snippet`, `body_html`, `body_text`, `sender_name`, `sender_email`, `to_addresses`, `cc_addresses`, `is_read`, `is_starred`, `has_attachments`, `category`, `labels`, `scheduled_for`, `received_at`, `created_at`, `updated_at`)
SELECT
    'spam',
    'Undian Berhadiah Tidak Resmi',
    'Selamat Anda mendapatkan hadiah besar, klik tautan berikut untuk klaim.',
    '<p>Selamat! Anda terpilih mendapatkan hadiah senilai Rp250 juta. Segera klaim dengan mengisi formulir berikut.</p>',
    'Selamat! Anda terpilih mendapatkan hadiah senilai Rp250 juta. Segera klaim dengan mengisi formulir berikut.',
    'Undian Palsu',
    'hadiah@lotterypalsu.com',
    'redaksi@antara.id',
    NULL,
    0,
    0,
    0,
    'spam',
    'External',
    NULL,
    NOW() - INTERVAL 4 HOUR,
    NOW(),
    NOW()
WHERE NOT EXISTS (SELECT 1 FROM `email_messages` WHERE `subject` = 'Undian Berhadiah Tidak Resmi' LIMIT 1);

INSERT INTO `email_messages`
    (`folder`, `subject`, `snippet`, `body_html`, `body_text`, `sender_name`, `sender_email`, `to_addresses`, `cc_addresses`, `is_read`, `is_starred`, `has_attachments`, `category`, `labels`, `scheduled_for`, `received_at`, `created_at`, `updated_at`)
SELECT
    'deleted',
    'Draft Press Release Lama',
    'Draft lama yang perlu ditinjau ulang sebelum dipublikasi.',
    '<p>Halo tim,</p><p>Draft press release versi lama saya pindahkan ke Deleted supaya tidak salah kirim. Jika masih diperlukan, silakan restore.</p>',
    'Halo tim,\n\nDraft press release versi lama saya pindahkan ke Deleted supaya tidak salah kirim. Jika masih diperlukan, silakan restore.',
    'Divisi PR Antara',
    'pr@antara.id',
    'kolaborasi@antara.id',
    NULL,
    1,
    0,
    0,
    'archive',
    'Projects,Internal',
    NULL,
    NOW() - INTERVAL 2 DAY,
    NOW(),
    NOW()
WHERE NOT EXISTS (SELECT 1 FROM `email_messages` WHERE `subject` = 'Draft Press Release Lama' LIMIT 1);
