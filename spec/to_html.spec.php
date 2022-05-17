<?php

declare(strict_types=1);

require_once './src/vnode.php';

use function vnode\to_html;
use function vnode\from_html;
use function vnode\el;
use function vnode\raw;

describe("to_html", function () {
});

describe("to_html(from_html('<my-tag/>'))", function () {
    it("returns an empty element: `<my-tag></my-tag>`", function () {
        expect(to_html(from_html('<my-tag/>')))->toBe('<my-tag></my-tag>');
    });
});

describe("to_html(from_html('<br>'))", function () {
    it("recognizes self closing tags: `<br>`", function () {
        expect(to_html(from_html('<br>')))->toBe('<br>');
    });
});

describe("to_html(from_html('<div/><input><img><span/>'))", function () {
    it("handles fragments: '<div></div><input><img><span></span>'", function () {
        expect(to_html(from_html('<div/><input><img><span/>')))
            ->toBe('<div></div><input><img><span></span>');
    });
});

describe("to_html(['text', el('div'), el('input'), el('img'), el('span')]))", function () {
    it("handles fragments: 'text<div></div><input><img><span></span>'", function () {
        expect(to_html(['text', el('div'), el('input'), el('img'), el('span')]))
            ->toBe('text<div></div><input><img><span></span>');
    });
});

describe("to_html('<a href=\"javascript:xss()\">')", function () {
    it("escapes text by default: '&lt;a href=&quot;javascript:xss()&quot;&gt;'", function () {
        expect(to_html('<a href="javascript:xss()">'))
            ->toBe('&lt;a href=&quot;javascript:xss()&quot;&gt;');
    });
});

describe("to_html(raw('<a href=\"javascript:xss()\">'))", function () {
    it("handles unescaped text with `raw()`: '<a href=\"javascript:xss()\">'", function () {
        expect(to_html(raw('<a href="javascript:xss()">')))
            ->toBe('<a href="javascript:xss()">');
    });
});

describe("to_html([null, 'text', null, 'more text'])", function () {
    it("removes `null` and merges adjacent text: 'textmore text'", function () {
        expect(to_html([null, 'text', null, 'more text']))
            ->toBe('textmore text');
    });
});

describe("to_html([1, .99, true, false])", function () {
    it("converts numbers to `string`, `true` to 1 and `false` to '': '10.991'", function () {
        expect(to_html([1, .99, true, false]))
            ->toBe('10.991');
    });
});


describe("to_html(el('input', ['selected', 'focused', 'value'=>'value']))", function () {
    it("handles boolean attributes: '<input selected focused=\"focused\" value=\"value\">'", function () {
        expect(to_html(el('input', ['selected', 'focused', 'value'=>'value'])))
            ->toBe('<input selected focused="focused" value="value">');
    });
});
