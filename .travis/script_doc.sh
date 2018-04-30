#!/usr/bin/env sh

cd doc && sphinx-build -W -b html -d _build/doctrees . _build/html
