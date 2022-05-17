<?php

declare(strict_types=1);

require_once './src/vnode.php';

use function vnode\el;
use function vnode\raw;
use function vnode\comma;
use function vnode\multival;

describe("el", function () {
});

function t1($actual)
{
    return function () use ($actual) {
        $this->actual = $actual;
        it("returns a `vnode\Vnode` instance", function () {
            expect($this->actual)->toBeAnInstanceOf('vnode\Vnode');
        });

        it("has a `tag` property, which is the string `div`", function () {
            expect($this->actual->tag)->toBe('div');
            expect($this->actual->tag)->toBeA('string');
        });

        it("has an `attributes` property, which is an empty array", function () {
            expect($this->actual->attributes)->toBe([]);
            expect($this->actual->attributes)->toBeA('array');
        });

        it("has a `children` property, which is an empty array", function () {
            expect($this->actual->children)->toBe([]);
            expect($this->actual->children)->toBeA('array');
        });
    };
}

describe("el('div', [], [])", function () {
    $d = <<< 'EOD'
       Takes three arguments
       - A `string` as the tag name
       - An `array` as the properties
       - An `array` of children
       EOD;
    describe($d, t1(el('div', [], [])));
});

describe("el('div', [])", function () {
    describe("the third argument is optional. A single array is considered to be the attributes", t1(el('div', [])));
});

describe("el('div')", function () {
    describe("second and third argument are optional", t1(el('div')));
});

describe("`el(1234567)` or `el([])`", function () {
    it("throws a `TypeError`, the first argument has to be a string", function () {
        expect(function () {
            el(1234567);
        })->toThrow(new TypeError());
        expect(function () {
            el([]);
        })->toThrow(new TypeError());
    });
});

describe("el('invalid tag name @#$')", function () {
    describe("the tag name", function () {
        it("is not validated", function () {
            expect(el('invalid tag name @#$')->tag)->toBe('invalid tag name @#$');
        });
    });
});

describe("el('a', ['href'=>'/link', 'id'=>'Anchor', 'class'=>'btn primary'])", function () {
    $this->actual = el('a', ['href' => '/link', 'id' => 'Anchor', 'class' => 'btn primary']);
    it("represents attributes as an array of key value pairs", function () {
        expect($this->actual->attributes)->toBeAn('array');
        expect($this->actual->attributes)->toBe(['href' => '/link', 'id' => 'Anchor', 'class' => 'btn primary']);
    });
    describe('the attribute key', function () {
        it("is always a string", function () {
            foreach ($this->actual->attributes as $key => $val) {
                expect($key)->toBeA('string');
            }
        });
    });
});

describe("el('a', ['ninety 9 red &(|'=>'ballons'])", function () {
    describe('the attribute key', function () {
        it("is not validated", function () {
            expect(el('a', ['ninety 9 red &(|' => 'ballons'])->attributes)->toBe(['ninety 9 red &(|' => 'ballons']);
        });
    });
});

describe("`el('input', ['value'=>33])` or `el('input', ['value'=>13.579])`", function () {
    describe('the attribute value', function () {
        it("can be a number", function () {
            expect(el('input', ['value' => 33])->attributes['value'])->toBeAn('integer');
            expect(el('input', ['value' => 13.579])->attributes['value'])->toBeA('float');
        });
    });
});

describe("`el('input', ['value'=>false])` or `el('input', ['value'=>true])`", function () {
    describe('the attribute value', function () {
        it("can be a boolean", function () {
            expect(el('input', ['value' => false])->attributes['value'])->toBeA('boolean');
            expect(el('input', ['value' => true])->attributes['value'])->toBeA('boolean');
        });
    });
});

describe("el('input', ['value'=>null])", function () {
    describe('the attribute', function () {
        it("is removed when its value is null", function () {
            expect(el('input', ['value'  =>  null])->attributes)->toBe([]);
        });
    });
});

describe("el('input', ['selected'])", function () {
    describe('the attribute value can be a boolean attribute', function () {
        it("has a key that is the same as the value", function () {
            expect(el('input', ['selected'])->attributes['selected'])->toBe('selected');
        });
    });
});

describe("el('input', ['class'=>['form-field', 'primary', 'red']])", function () {
    describe('the attribute value', function () {
        $this->actual = el('input', ['class'  =>  ['form-field', 'primary', 'red']])->attributes['class'];
        it("can be an array", function () {
            expect($this->actual->values)->toBe(['form-field', 'primary', 'red']);
        });
        it("is an instance of `vnode\AttributeMultiValue`", function () {
            expect($this->actual)->toBeAnInstanceOf('vnode\AttributeMultiValue');
        });
        it("separates the elements with a ` ` by default", function () {
            expect($this->actual->sep)->toBe(' ');
        });
    });
});

describe("el('input', ['type'=>'file', 'accept'=>multival(['image/gif', 'image/jpeg'], ',')])", function () {
    describe('the attribute value', function () {
        $this->actual = el('input', ['type'  =>  'file', 'accept'  =>  multival(['image/gif', 'image/jpeg'], ',')])
                      ->attributes['accept'];
        it("can be created with `multival(array, sep)` to specify the separator character", function () {
            expect($this->actual->values)->toBe(['image/gif', 'image/jpeg']);
        });
        it("separates the elements with the specified char: `,`", function () {
            expect($this->actual->sep)->toBe(',');
        });
    });
});

describe("el('input', ['type'=>'file', 'accept'=>comma(['image/gif', 'image/jpeg']])", function () {
    describe('the attribute value', function () {
        $this->actual = el('input', ['type' => 'file', 'accept' => comma(['image/gif', 'image/jpeg'])])
                      ->attributes['accept'];
        it("can be created with `comma(array)` to specify the `,` as the separator character", function () {
            expect($this->actual->values)->toBe(['image/gif', 'image/jpeg']);
        });
        it("separates the elements with the specified char: `,`", function () {
            expect($this->actual->sep)->toBe(',');
        });
    });
});

describe("comma('image/gif', 'image/jpeg')", function () {
    describe('the attribute value', function () {
        $this->actual = comma('image/gif', 'image/jpeg');
        it("can be created with `comma(item1, item ...)` instead of an explicit array", function () {
            expect($this->actual->values)->toBe(['image/gif', 'image/jpeg']);
        });
        it("separates the elements with the specified char: `,`", function () {
            expect($this->actual->sep)->toBe(',');
        });
    });
});

describe("`comma('image/gif', [null, 'image/jpeg'])` or `comma(['image/gif', null, 'image/jpeg'])`", function () {
    it("flattens arrays and removes null values", function () {
        expect(comma('image/gif', [null, ['image/jpeg']])->values)->toBe(['image/gif', 'image/jpeg']);
        expect(comma(['image/gif', null, 'image/jpeg'])->values)->toBe(['image/gif', 'image/jpeg']);
    });
});

describe("el('h1', 'text child', el('br'))", function () {
    describe(
        "multiple parameters, where the first one is not `vnode\AttributeMultiValue` or \
`array`\nare considered children",
        function () {
            $this->actual = el('h1', 'text child 1', el('br'));
            it("has no attributes", function () {
                expect($this->actual->attributes)->toBe([]);
            });
            it("has two children", function () {
                expect($this->actual->children)->toHaveLength(2);
            });
        }
    );
});

describe("el('h1', 'text', 123, 456.789, true, false, el('em', 'more text'))", function () {
    describe("children can be `string`, `integer`, `float`, `boolean` and `vnode\Vnode`", function () {
        $this->actual = el('h1', 'text', 123, 456.789, true, false, el('em', 'more text'))->children;
        it("has six children", function () {
            expect($this->actual)->toHaveLength(6);
        });
        it("contains `'text'` 1st", function () {
            expect($this->actual[0])->toBe('text');
        });
        it("contains `123` 2nd", function () {
            expect($this->actual[1])->toBe(123);
        });
        it("contains `456.789` 3rd", function () {
            expect($this->actual[2])->toBe(456.789);
        });
        it("contains `true` 4th", function () {
            expect($this->actual[3])->toBe(true);
        });
        it("contains `false` 5th", function () {
            expect($this->actual[4])->toBe(false);
        });
        it("contains a `vnode\Vnode` 6th", function () {
            expect($this->actual[5])->toBeAnInstanceOf('vnode\Vnode');
        });
    });
});

describe("el('h1', el('br'), [[null, ['text element']], null, 123456])", function () {
    describe("children arrays are flattened and null values are removed", function () {
        $this->actual = el('h1', el('br'), [[null, ['text element']], null, 123456])->children;
        it("has three children", function () {
            expect($this->actual)->toHaveLength(3);
        });
        it("contains a `vnode\Vnode` 1st", function () {
            expect($this->actual[0])->toBeAnInstanceOf('vnode\Vnode');
        });
        it("contains `'text element'` 2nd", function () {
            expect($this->actual[1])->toBe('text element');
        });
        it("contains `123456` 3rd", function () {
            expect($this->actual[2])->toBe(123456);
        });
    });
});

describe(
    "el(function (\$atts, \$chn) {return el('div', \$atts, \$chn);}, ['id'=>'X'], el('h1', 'Title'))",
    function () {
        $desc = <<< 'EOD'
          The first argument can be a `callable` that returns a `Vnode`.
          It is invoked with `$attributes` and `$children` as arguments.
          EOD;
        describe($desc, function () {
            $f = function ($as, $cn) {
                return el('div', $as, $cn);
            };
            $this->actual = $res = el($f, ['id' => 'X'], el('h1', 'Title'));
            it("returns a `vnode`", function () {
                expect($this->actual)->toBeAnInstanceOf('vnode\Vnode');
            });
        });
    }
);
