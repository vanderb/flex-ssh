# FlexSSH

Manage your SSH-Aliases via Terminal.

## Installation

`composer global require vanderb/flex-ssh`

## First Steps

### Install

To install required files and configs just run `flex-ssh install`.

## Command-List

| Command         | Description           |
| --------------- | :-------------------- |
| flex-ssh ls     | List all aliases      |
| flex-ssh make   | Make new alias        |
| flex-ssh update | Update existing alias |
| flex-ssh delete | Delete existing alias |

### List

List all: `flex-ssh ls`

List specific alias: `flex-ssh ls --alias={ALIAS}`

List all aliases by specific host: `flex-ssh ls --host={HOST}`

### Make

To create new alias: `flex-ssh make myAlias`

Create new alias with direct options: `flex-ssh make {ALIAS} --host={HOST} --user={USER} --port={PORT}`

Create new alias with quick notation as host-option `flex-ssh make {ALIAS} --q={USER}@{HOST}:{PORT}`

### Update

To update existing alias: `flex-ssh update {ALIAS}`

You will prompt to enter updated data. Just hit enter, if single information does not changed.

### Delete

To delete existing alias: `flex-ssh delete {ALIAS}`

You will prompt to confirm the deletion.

## Side notes

This package was created with the help of [Laravel Zero](https://laravel-zero.com/).

It is still in developement. So there may be bugs. If you found any please let me know in issue-section. Thank you!
