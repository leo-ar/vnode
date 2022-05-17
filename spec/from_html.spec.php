<?php

declare(strict_types=1);

require_once './src/vnode.php';

use function vnode\from_html;

describe("from_html", function () {
});

describe("from_html('<my-tag/>')", function () {
    $this->actual = from_html('<my-tag/>');
    $this->vnode = $this->actual[0];
    it("returns an array of one vnode", function () {
        expect($this->actual)->toHaveLength(1);
        expect($this->vnode)->toBeAnInstanceOf('vnode\Vnode');
    });
    describe("the `vnode`", function () {
        it("has a `my-tag` tag", function () {
            expect($this->vnode->tag)->toBe('my-tag');
        });
        it("has a `[]` attributes", function () {
            expect($this->vnode->attributes)->toBe([]);
        });
        it("has a `[]` children", function () {
            expect($this->vnode->children)->toBe([]);
        });
    });
});

describe("from_html('<my-tag my-attr=\"my-val\"/>')", function () {
    $this->actual = from_html('<my-tag my-attr="my-val"/>');
    it("returns an array of one vnode", function () {
        expect($this->actual)->toHaveLength(1);
        expect($this->actual[0])->toBeAnInstanceOf('vnode\Vnode');
    });
    it("returns a vnode with attrs", function () {
        expect($this->actual[0]->tag)->toBe('my-tag');
        expect($this->actual[0]->attributes)->toBe(['my-attr'=>'my-val']);
    });
});

describe("from_html('<my-tag/><your-tag/>')", function () {
    $this->actual = from_html('<my-tag/><your-tag/>');
    it("returns an array of two vnodes", function () {
        expect($this->actual)->toHaveLength(2);
        foreach ($this->actual as $vnode) {
            expect($vnode)->toBeAnInstanceOf('vnode\Vnode');
        }
    });
});

describe("from_html('<my-tag/>some text<your-tag/>more text')", function () {
    $this->actual = from_html('<my-tag/>some text<your-tag/>more text');
    it("returns an array of four elements", function () {
        expect($this->actual)->toHaveLength(4);
        expect($this->actual[0])->toBeAnInstanceOf('vnode\Vnode');
        expect($this->actual[1])->toBeA('string');
        expect($this->actual[2])->toBeAnInstanceOf('vnode\Vnode');
        expect($this->actual[3])->toBeA('string');
    });
});
