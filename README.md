Dual site sitemap.xml generator
===========================

Source code for my blog post about sitemap.xml: http://mosolov.wordpress.com/2014/09/27/%D0%BA%D0%B0%D0%BA-%D1%8F-sitemap-xml-%D0%B4%D0%BB%D1%8F-%D0%B2%D0%B0%D1%88%D0%B5%D0%B3%D0%BE-%D1%81%D0%B0%D0%B9%D1%82%D0%B0-%D0%B4%D0%B5%D0%BB%D0%B0%D0%BB/

Usage
-----

```
$ git clone https://github.com/denismosolov/dual-site-sitemap-generator.git
$ cd dual-site-sitemap-generator
$ composer install
$ mv config.dist.php config.php
```

Run
---
```
$ php index.php > sitemap.xml
```