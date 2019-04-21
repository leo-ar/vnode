# vnode

Create composable, structured html with php.

## API

### Constructing: `el`
```
el('div', [], [])
  Takes three arguments
  - A `string` as the tag name
  - An `array` as the properties
  - An `array` of children
    ✓ it returns a `vnode\Vnode` instance
    ✓ it has a `tag` property, which is the string `div`
    ✓ it has an `attributes` property, which is an empty array
    ✓ it has a `children` property, which is an empty array

el('div', [])
  the third argument is optional. A single array is considered to be the attributes
    ✓ it returns a `vnode\Vnode` instance
    ✓ it has a `tag` property, which is the string `div`
    ✓ it has an `attributes` property, which is an empty array
    ✓ it has a `children` property, which is an empty array

el('div')
  second and third argument are optional
    ✓ it returns a `vnode\Vnode` instance
    ✓ it has a `tag` property, which is the string `div`
    ✓ it has an `attributes` property, which is an empty array
    ✓ it has a `children` property, which is an empty array

`el(1234567)` or `el([])`
  ✓ it throws a `TypeError`, the first argument has to be a string

el('invalid tag name @#$')
  the tag name
    ✓ it is not validated

el('a', ['href'=>'/link', 'id'=>'Anchor', 'class'=>'btn primary'])
  ✓ it represents attributes as an array of key value pairs
  the attribute key
    ✓ it is always a string

el('a', ['ninety 9 red &(|'=>'ballons'])
  the attribute key
    ✓ it is not validated

`el('input', ['value'=>33])` or `el('input', ['value'=>13.579])`
  the attribute value
    ✓ it can be a number

`el('input', ['value'=>false])` or `el('input', ['value'=>true])`
  the attribute value
    ✓ it can be a boolean

el('input', ['value'=>null])
  the attribute
    ✓ it is removed when its value is null

el('input', ['selected'])
  the attribute value can be a boolean attribute
    ✓ it has a key that is the same as the value

el('input', ['class'=>['form-field', 'primary', 'red']])
  the attribute value
    ✓ it can be an array
    ✓ it is an instance of `vnode\AttributeMultiValue`
    ✓ it separates the elements with a ` ` by default

el('input', ['type'=>'file', 'accept'=>multival(['image/gif', 'image/jpeg'], ',')])
  the attribute value
    ✓ it can be created with `multival(array, sep)` to specify the separator character
    ✓ it separates the elements with the specified char: `,`

el('input', ['type'=>'file', 'accept'=>comma(['image/gif', 'image/jpeg']])
  the attribute value
    ✓ it can be created with `comma(array)` to specify the `,` as the separator character
    ✓ it separates the elements with the specified char: `,`

comma('image/gif', 'image/jpeg')
  the attribute value
    ✓ it can be created with `comma(item1, item ...)` instead of an explicit array
    ✓ it separates the elements with the specified char: `,`

`comma('image/gif', [null, 'image/jpeg'])` or `comma(['image/gif', null, 'image/jpeg'])`
  ✓ it flattens arrays and removes null values

el('h1', 'text child', el('br'))
  multiple parameters, where the first one is not `vnode\AttributeMultiValue` or `array`
  are considered children
    ✓ it has no attributes
    ✓ it has two children

el('h1', 'text', 123, 456.789, true, false, el('em', 'more text'))
  children can be `string`, `integer`, `float`, `boolean` and `vnode\Vnode`
    ✓ it has six children
    ✓ it contains `'text'` 1st
    ✓ it contains `123` 2nd
    ✓ it contains `456.789` 3rd
    ✓ it contains `true` 4th
    ✓ it contains `false` 5th
    ✓ it contains a `vnode\Vnode` 6th

el('h1', el('br'), [[null, ['text element']], null, 123456])
  children arrays are flattened and null values are removed
    ✓ it has three children
    ✓ it contains a `vnode\Vnode` 1st
    ✓ it contains `'text element'` 2nd
    ✓ it contains `123456` 3rd

el(function ($atts, $chn) {return el('div', $atts, $chn);}, ['id'=>'X'], el('h1', 'Title'))
  The first argument can be a `callable` that returns a `Vnode`.
  It is invoked with `$attributes` and `$children` as arguments.
    ✓ it returns a `vnode`
```

### Constructing: `from_html`
```
from_html('<my-tag/>')
  ✓ it returns an array of one vnode
  the `vnode`
    ✓ it has a `my-tag` tag
    ✓ it has a `[]` attributes
    ✓ it has a `[]` children

from_html('<my-tag my-attr="my-val"/>')
  ✓ it returns an array of one vnode
  ✓ it returns a vnode with attrs

from_html('<my-tag/><your-tag/>')
  ✓ it returns an array of two vnodes

from_html('<my-tag/>some text<your-tag/>more text')
  ✓ it returns an array of four elements
```

### Serializing: `to_html`
```
to_html(from_html('<my-tag/>'))
  ✓ it returns an empty element: `<my-tag></my-tag>`

to_html(from_html('<br>'))
  ✓ it recognizes self closing tags: `<br>`

to_html(from_html('<div/><input><img><span/>'))
  ✓ it handles fragments: '<div></div><input><img><span></span>'

to_html(['text', el('div'), el('input'), el('img'), el('span')]))
  ✓ it handles fragments: 'text<div></div><input><img><span></span>'

to_html('<a href="javascript:xss()">')
  ✓ it escapes text by default: '&lt;a href=&quot;javascript:xss()&quot;&gt;'

to_html(raw('<a href="javascript:xss()">'))
  ✓ it handles unescaped text with `raw()`: '<a href="javascript:xss()">'

to_html([null, 'text', null, 'more text'])
  ✓ it removes `null` and merges adjacent text: 'textmore text'

to_html([1, .99, true, false])
  ✓ it converts numbers to `string`, `true` to 1 and `false` to '': '10.991'

to_html(el('input', ['selected', 'focused', 'value'=>'value']))
  ✓ it handles boolean attributes: '<input selected focused="focused" value="value">'
```

## To do for v1-alpha
- [x] Serialize to html
- [x] `unsafe` text
- [x] Handle boolean attributes
- [x] Construct `vnode` tree from html/xml description
- [x] Enable components through callables

## To do for v1-release
- [ ] Stabilize the API
- [ ] Complete tests
- [ ] Add documentation
  - [ ] phpdoc
  - [ ] README
    - [ ] Getting started
    - [ ] Installing
    - [ ] Usage

## Ideas for post v1
- [ ] Serialize to source code
- [ ] Construct DOM instead of `vnode`
- [ ] Serialize to xml
- [ ] Utility functions for `class`, `style` and `data-*` attributes
  - See [snabbdom modules](https://github.com/snabbdom/snabbdom#modules-documentation)
- [ ] Control the creation and serialization process from the caller through callbacks
- [ ] Define release process
- [ ] Define contribution process
- [ ] Add CHANGELOG
- [ ] Set development php version with [asdf](https://asdf-vm.com/)

<!--
## Getting Started

```
examples
```


### Installing

```
example
```
-->
## Versioning

We use [SemVer](http://semver.org/) for versioning. For the versions available, see the [tags on this repository](https://github.com/leo-ar/vnode/tags).


## License

This project is licensed under the [GNU Lesser General Public License v3.0 or later](https://www.gnu.org/licenses/lgpl-3.0-standalone.html).


## Contributing

Please read [CONTRIBUTING.md](CONTRIBUTING.md) for details.


## Inspiration

- [HyperScript](https://github.com/hyperhype/hyperscript)
- [RE:DOM](https://github.com/redom/redom)
