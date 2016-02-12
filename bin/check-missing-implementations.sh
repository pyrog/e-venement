#!/bin/bash

echo "Those templates has no implementation and it can be missing: "
for elt in `find -iname '*.template'`; do [ ! -e `echo $elt | sed 's/.template$//'` ] && echo "TODO: $elt"; done

