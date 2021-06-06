# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## 2.0.0 - 2021-06-06
- Remove support for php 7 (now package works only with php 8)
- Remove bool type for enum member values
- Allow strict typing
- Add implementing ```Stringable``` interface
- Now ```__toString()``` returns ```(string) $this->value```
- Add ```id()``` method for return unique string for enum member (composed of FQCN and enum member name)
- Add ```toArray()``` method
- Add ```toValues()``` and ```toNames``` methods

## 1.0.0 - 2020-11-10
- Basic package functionality
