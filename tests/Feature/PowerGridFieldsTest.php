<?php

use PowerComponents\LivewirePowerGrid\Tests\Concerns\Components\OrderTable;
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Models\Order;

use function PowerComponents\LivewirePowerGrid\Tests\Plugins\livewire;

it('removes <script> tag from custom field', function (string $component, object $params) {
    Order::first()->update(['link' => 'hello there! <script>alert(document.cookie)</script>']);

    livewire($component)
        ->call('setTestThemeClass', $params->theme)
        ->assertDontSeeHtml('&lt;script&gt;alert')
        ->assertDontSeeHtml('<script>alert')
        ->assertSeeHtml('hello there!');
})->with('order table');

it('runs e() helper in PG fields', function (string $component, object $params) {
    Order::first()->update(['name' => '<img src="invalid_url.png" onerror=alert(document.cookie)>']);

    livewire($component)
        ->call('setTestThemeClass', $params->theme)
        ->assertDontSeeHtml('<img src="invalid_url.png"')
        ->assertSeeHtml('<div>&lt;img src=&quot;invalid_url.png&quot; onerror=alert(document.cookie)&gt;');
})->with('order table');

it('does not run e() in custom PG fields', function (string $component, object $params) {
    $link = '<a href="https://google.com" target="_blank">Link from closure</a>';

    Order::first()->update(['link' => $link]);

    livewire($component)
        ->call('setTestThemeClass', $params->theme)
        ->assertDontSeeHtml(e($link))
        ->assertSeeHtml($link);
})->with('order table');

it('can fields with casting and custom fields', function (string $component, object $params) {
    livewire($component)
        ->call('setTestThemeClass', $params->theme)
        ->assertSeeHtmlInOrder(['Order 1', 'Order 2', 'Order 3'])
        ->assertSeeHtmlInOrder(['active', 'active', 'inactive'])
        ->assertSeeHtmlInOrder(['1000', '2000', '0'])
        ->assertSeeHtmlInOrder(['127.3', '259.5', '']);
})->with('order table');

dataset('order table', [
    'tailwind'  => [OrderTable::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Tailwind::class]],
    'bootstrap' => [OrderTable::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Bootstrap5::class]],
]);
