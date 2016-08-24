#!/bin/sh

DATA_DIR="/media/jayr/aruna"

rm -rfv $DATA_DIR/*
sqlite3 $DATA_DIR/aruna_db.sq3 < /vagrant/resources/bootstrap_db.sql
