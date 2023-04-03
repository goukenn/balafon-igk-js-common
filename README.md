# JSExpression module

help to stringify php data to JS language 

```php
JExpression::Stringify(["one", ":help", ":8 + 80"])
```
will produce
```js
["one", help, 8 + 80]
```

Also detect string with entry function

```php
JExpression::Stringify("data(){ return 8}");
```

will return 
```JS
data(){ return 8}
```




