# Orbiter

Orbiter is a very minimalistic web content manager inspired by [Project Gemini](https://gemini.circumlunar.space/) and written in PHP. It takes `text/gemini` files as a source to display them as HTML pages, which can be rendered by any web browser. 

## Features

* Simple: You can write content just by editing text files (in a Markdown-ish format).
* Lightweight: The setup is as easy as copying a few files into your web server.
* Compatible: You can use the same content directory for a Gemini server.

## Setup

Docker setup, useful for testing it locally:

```
docker build .
docker run -d -p 8082:80 --mount type=bind,source="$(pwd)/htdocs",target=/var/www/html <image name>
```

Apache setup:

* Install the pre-requirements: apache, mod_php, mod_rewrite.
* Copy the `htdocs` directory into your Apache document root.
* Write content by editing the files in the `content` directory. If you have to use a different directory, like the directory of your Gemini server, configure it in `config.php`.
* You can customize the page style by editing `assets/style.css`.

## Author

[Asier Aguado](https://github.com/asieraguado)

## License

* [Apache License, Version 2.0](https://www.apache.org/licenses/LICENSE-2.0)
