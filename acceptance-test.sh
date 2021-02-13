#!/bin/sh

## acceptance環境に変更する
touch public/acceptance

vendor/bin/codecept -vvv run acceptance --steps $@

## acceptance環境を止める
rm public/acceptance
