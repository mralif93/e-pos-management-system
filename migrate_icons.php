<?php

$dir = new RecursiveDirectoryIterator(__DIR__ . '/resources/views');
$ite = new RecursiveIteratorIterator($dir);
$files = new RegexIterator($ite, '/\.blade\.php$/', RegexIterator::MATCH);

$materialMap = [
    'add' => 'hgi-add-01',
    'all_inbox' => 'hgi-inbox',
    'arrow_back' => 'hgi-arrow-left-01',
    'cancel' => 'hgi-cancel-01',
    'category' => 'hgi-grid-view',
    'check_circle' => 'hgi-tick-circle',
    'close' => 'hgi-cancel-01',
    'credit_card' => 'hgi-credit-card',
    'delete' => 'hgi-delete-01',
    'edit' => 'hgi-edit-02',
    'edit_square' => 'hgi-edit-01',
    'email' => 'hgi-mail-01',
    'error' => 'hgi-alert-01',
    'filter_list' => 'hgi-filter',
    'inventory' => 'hgi-warehouse',
    'inventory_2' => 'hgi-package',
    'list_alt' => 'hgi-list',
    'local_shipping' => 'hgi-truck-01',
    'lock' => 'hgi-lock-password',
    'lock_outline' => 'hgi-lock-password',
    'lock_reset' => 'hgi-user-unlock-01',
    'login' => 'hgi-login-01',
    'mail' => 'hgi-mail-01',
    'manage_accounts' => 'hgi-user-setting-01',
    'money' => 'hgi-money-01',
    'password' => 'hgi-lock-password',
    'payments' => 'hgi-cash-01',
    'people' => 'hgi-user-multiple-02',
    'person_add' => 'hgi-user-add-01',
    'point_of_sale' => 'hgi-computer',
    'receipt_long' => 'hgi-invoice-01',
    'refresh' => 'hgi-setup-01', // Wait, hgi-refresh-01 doesn't seem to exist or does it? Wait, let's use hgi-reload
    'save' => 'hgi-floppy-disk-01',
    'schedule' => 'hgi-calendar-01',
    'search' => 'hgi-search-01',
    'security' => 'hgi-shield-01',
    'send' => 'hgi-sent',
    'star' => 'hgi-star',
    'store' => 'hgi-store-01',
    'swap_horiz' => 'hgi-arrow-left-right',
    'trending_up' => 'hgi-chart-increase',
    'tune' => 'hgi-settings-02',
    'verified_user' => 'hgi-user-check-01',
    'visibility' => 'hgi-view',
    'warning' => 'hgi-alert-02',
];

$boxiconMap = [
    'bxs-store-alt' => 'hgi-store-01',
    'bxs-sun' => 'hgi-sun-01',
    'bxs-moon' => 'hgi-moon-01',
    'bxs-component' => 'hgi-computer',
    'bxl-twitter' => 'hgi-twitter',
    'bxl-github' => 'hgi-github',
    'bxl-linkedin-square' => 'hgi-linkedin-01',
];

$count = 0;
foreach ($files as $file) {
    if (strpos($file->getFilename(), '.blade.php') === false)
        continue;

    $path = $file->getPathname();
    $content = file_get_contents($path);
    $original = $content;

    // Replace material icons
    foreach ($materialMap as $mat => $hgi) {
        // match <span class="material-icons{maybe additional classes}">{mat}</span>
        $pattern = '/<span\s+class="material-icons([^"]*)"[^>]*>\s*' . preg_quote($mat, '/') . '\s*<\/span>/is';

        $content = preg_replace_callback($pattern, function ($matches) use ($hgi) {
            $classes = trim($matches[1]);
            if ($classes) {
                return '<i class="hgi-stroke text-[20px] ' . $hgi . ' ' . $classes . '"></i>';
            }
            return '<i class="hgi-stroke text-[20px] ' . $hgi . '"></i>';
        }, $content);
    }

    // Replace boxicons
    foreach ($boxiconMap as $bx => $hgi) {
        // match <i class="bx bxs-store-alt {classes}"></i> OR <i class='bx bxs-store-alt {classes}'></i>
        $pattern = '/<i\s+class=([\'"])bx\s+' . preg_quote($bx, '/') . '([^\'"]*)\1[^>]*><\/i>/is';

        $content = preg_replace_callback($pattern, function ($matches) use ($hgi) {
            $classes = trim($matches[2]);
            if ($classes) {
                return '<i class="hgi-stroke text-[20px] ' . $hgi . ' ' . $classes . '"></i>';
            }
            return '<i class="hgi-stroke text-[20px] ' . $hgi . '"></i>';
        }, $content);
    }

    if ($content !== $original) {
        file_put_contents($path, $content);
        echo "Updated $path\n";
        $count++;
    }
}

echo "Total files updated: $count\n";
