# Changes Required for `laminas/mvc`

The URL view helper factory is a [closure created here](https://github.com/laminas/laminas-mvc/blob/3.4.x/src/Service/ViewHelperManagerFactory.php#L73-L100). This closure will need to be changed to inject the router and route match instances into the helper constructor, or, removed completely to favour the shipped `UrlFactory`.

The [Doctype factory in ViewHelperManagerFactory](https://github.com/laminas/laminas-mvc/blob/3.4.x/src/Service/ViewHelperManagerFactory.php#L131-L152) can be dropped in favour of the shipped factory.
