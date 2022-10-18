# MyFeeds

Demo module. Absolutely not safe for production.

## Installation

* Create a node type `feed`
* Add a new `entity_reference` field on Users and select `feed` + unlimited cardinality in settings (see [this video](https://www.loom.com/share/f3af65cb8e3a47898ff3283b174580f7))
* Download an place this module under `web/modules/custom` 
* Enable the module 
* Clear cache (e.g. Admin > Config > Development > Performances)
* Place the new extra field on Feed default display (see [this video](https://www.loom.com/share/6861b6ea55074b16800eba4599e7d53e))

Now you should have a field displayed on front end when viewing a `node:feed`.

Enjoy!

--- 

Feel free [to contact me anytime](https://matthieuscarset.com)
