<?php

test('deployment preserves an existing application key', function () {
    $script = file_get_contents(dirname(__DIR__, 2).'/deploy.sh');

    expect($script)
        ->toContain("grep -Eq '^APP_KEY=.+$' .env")
        ->not->toContain('grep -q "APP_KEY=base64:" .env');
});
