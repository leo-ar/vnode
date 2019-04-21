<?php
declare(strict_types=1);

namespace vnode;

// ///////////////////////////////////////////////////////////////////
// Creation
// ///////////////////////////////////////////////////////////////////

// ///////////////////////////////////////////////////////////////////
// Attributes

class AttributeMultiValue {
    public $values;
    public $sep;

    public function __construct(array $values, string $sep) {
        $this->values = $values;
        $this->sep = $sep;
    }
}

function multival(array $values, string $sep=' '): AttributeMultiValue {
    return new AttributeMultiValue($values, $sep);
}

function comma(...$values): AttributeMultiValue {
    return new AttributeMultiValue(normalize_array($values), ',');
}

function is_multival($thing): bool {
    return $thing instanceof AttributeMultiValue;
}

function is_boolean_attribute($name): bool {
    return !is_string($name) || '' === $name;
}

function is_valid_attribute_name($name): bool {
    return ctype_alpha(mb_substr($name, 0, 1));
}

function normalize_attributes(array $array, array $res=[]): array {
    foreach ($array as $key => $val) {
        if (is_null($val)) continue;
        $attr_name = is_boolean_attribute($key) ? (string)$val : $key;
        if (!is_valid_attribute_name($attr_name)) throw new \DomainException('Invalid attribute name: ' . $attr_name);
        if (is_array($val)) $attr_value = multival($val, ' ');
        elseif (is_multival($val)) $attr_value = $val;
        else $attr_value = $val;
        $res[$attr_name] = $attr_value;
    }
    return $res;
}

// ///////////////////////////////////////////////////////////////////
// Children: Wrapped

interface iWrapped {
    public function unwrap(...$args);
}

class RawText implements iWrapped {
    private $value;

    public function __construct($value) {
        $this->value = (string)$value;
    }

    public function unwrap(...$args) {
       return $this->value;
    }
}

function raw($content): RawText {
    return new RawText($content);
}

// ///////////////////////////////////////////////////////////////////
// Children: normalize

function normalize_array(array $array, array $res=[]): array {
    foreach ($array as $val) {
        if (is_array($val)) $res = normalize_array($val, $res);
        elseif (is_null($val)) continue;
        else $res[] = $val;
    }
    return $res;
}

// ///////////////////////////////////////////////////////////////////
// Vnode

const NONE = [];

class Vnode {
    public $tag;
    public $attributes;
    public $children;

    public function __construct(string $tag, array $attributes, array $children) {
        $this->tag = $tag;
        $this->attributes = $attributes;
        $this->children = $children;
    }
}

function is_attributes($thing): bool {
    return is_multival($thing) || is_array($thing);
}

function el($tag, ...$rest): Vnode {
    $len = count($rest);
    if (0 === $len) {
        $attributes = NONE;
        $children = NONE;
    } elseif (1 === $len) {
        if (is_attributes($rest[0])) {
            $attributes = $rest[0];
            $children = NONE;
        } else {
            $attributes = NONE;
            $children = $rest;
        }
    } else {
        $attributes = is_attributes($rest[0]) ? array_shift($rest) : NONE;
        $children = $rest;
    }
    $attributes = normalize_attributes($attributes);
    $children = normalize_array($children);

    if (is_callable($tag)) return $tag($attributes, $children);
    else return new Vnode($tag, $attributes, $children);
}


// ///////////////////////////////////////////////////////////////////
// Serialization: HTML
// ///////////////////////////////////////////////////////////////////

const SELF_CLOSING = [
    'area' => true,
    'base' => true,
    'br' => true,
    'col' => true,
    'embed' => true,
    'hr' => true,
    'img' => true,
    'input' => true,
    'link' => true,
    'meta' => true,
    'param' => true,
    'source' => true,
    'track' => true,
    'wbr' => true,
    'command' => true,
    'keygen' => true,
    'menuitem' => true,
];

function is_self_closing(string $tag): bool {
    return array_key_exists($tag, SELF_CLOSING);
}

const BOOL_ATTR = [
    'allowfullscreen' => true,
    'allowpaymentrequest' => true,
    'async' => true,
    'autofocus' => true,
    'autoplay' => true,
    'checked' => true,
    'controls' => true,
    'default' => true,
    'defer' => true,
    'disabled' => true,
    'formnovalidate' => true,
    'hidden' => true,
    'ismap' => true,
    'itemscope' => true,
    'loop' => true,
    'multiple' => true,
    'muted' => true,
    'nomodule' => true,
    'novalidate' => true,
    'open' => true,
    'readonly' => true,
    'required' => true,
    'reversed' => true,
    'selected' => true,
    'typemustmatch' => true
];

function is_bool_attr(string $name): bool {
    return array_key_exists($name, BOOL_ATTR);
}

function multival_to_str(AttributeMultiValue $val): string {
    return join($val->sep, $val->values);
}

function attributes_to_html(array $attributes): string {
    $result = '';
    foreach ($attributes as $key => $val) {
        $value = is_multival($val) ? multival_to_str($val) : $val;
        $result .=  " " . (is_bool_attr($key) ? (string)$key : "$key=\"$value\"");
    }
    return $result;
}

function vnode_to_html(Vnode $vnode): string {
    $tag = $vnode->tag;
    $attributes = attributes_to_html($vnode->attributes);
    if (is_self_closing($tag)) return '<' . $tag . $attributes . ">";
    $children = array_to_html($vnode->children);
    return '<' . $tag . $attributes . ">" . $children . "</". $tag . ">";
}

function array_to_html(array $vnodes): string {
    $result = '';
    foreach ($vnodes as $v) $result .= to_html($v);
    return $result;
}

function is_vnode($thing): bool {
    return $thing instanceof Vnode;
}

function is_wrapped($thing): bool {
    return $thing instanceof iWrapped;
}

function to_html($thing): string {
    if (is_vnode($thing)) return vnode_to_html($thing);
    elseif (is_array($thing)) return array_to_html($thing);
    elseif (is_wrapped($thing)) return $thing->unwrap();
    elseif (is_null($thing)) return '';
    return htmlspecialchars((string)$thing);
}


// ///////////////////////////////////////////////////////////////////
// Parsing
// ///////////////////////////////////////////////////////////////////

function attributes_to_array(\DOMNamedNodeMap $atts): array {
    $res = [];
    foreach ($atts as $key => $val) $res[$key] = $val->textContent;
    return $res;
}

function elm_to_vnode(\DOMElement $elm): Vnode {
    $children = list_to_array($elm->childNodes);
    $attributes = attributes_to_array($elm->attributes);
    return el($elm->nodeName, $attributes, $children);
}

function list_to_array(\DOMNodeList $node_list): array {
    $res = [];
    foreach ($node_list as $item) {
        $type = $item->nodeType;
        if ($type === XML_TEXT_NODE) $res[] = $item->textContent;
        elseif ($type === XML_ELEMENT_NODE) $res[] = elm_to_vnode($item);
    }
    return $res;
}

function from_html(string $str): array {
    $doc = new \DOMDocument();
    libxml_use_internal_errors(true);
    $doc->loadHTML($str);
    libxml_clear_errors();
    $body = $doc->childNodes[1]->firstChild;
    return list_to_array($body->childNodes);
}
