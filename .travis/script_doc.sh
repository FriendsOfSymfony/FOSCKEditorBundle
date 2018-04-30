#!/usr/bin/env sh

cd docs && sphinx-build -W -b html -d _build/doctrees . _build/html
