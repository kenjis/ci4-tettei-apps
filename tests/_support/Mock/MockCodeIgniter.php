<?php

declare(strict_types=1);

namespace Tests\Support\Mock;

use App\MyCodeIgniter;

class MockCodeIgniter extends MyCodeIgniter
{
    protected function callExit($code): void
    {
        // Do not call exit() in testing.
    }
}
