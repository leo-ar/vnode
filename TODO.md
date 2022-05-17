# To do

## To do for v1

- [x] Serialize to html
- [x] `unsafe` text
- [x] Handle boolean attributes
- [x] Construct `vnode` tree from html/xml description
- [x] Enable components through callables

## To do after v1

- [ ] Review the API
- [ ] Complete tests
- [x] Add script to format the code
- [ ] Add documentation
  - [ ] phpdoc
  - [ ] README
    - [ ] Getting started
    - [ ] Installing
    - [ ] Usage

## Ideas for post v1

- 3 implementations of vnode:
  1. Vnodes as [DOM](https://www.php.net/manual/en/book.dom.php)
     - Useful to manipulate / transform the document programatically
  2. Vnodes as arrays
     - Implemented for v1
     - Not much use so far, maybe a `to_array()` is all it needs
     - If practical, it could be derived from the DOM representation
  3. `el()` returns text objects as html
     - In practice we haven't used post-processing
     - The general case is rendering to a string immediately
- [ ] Serialize to source code
- [ ] Serialize to xml
- [ ] Utility functions for `class`, `style` and `data-*` attributes
  - See [snabbdom modules](https://github.com/snabbdom/snabbdom#modules-documentation)
- [ ] Control the creation and serialization process from the caller through callbacks
- [ ] Define release process
- [ ] Define contribution process
- [ ] Add CHANGELOG
- [x] ~Set development php version with [asdf](https://asdf-vm.com/)~
