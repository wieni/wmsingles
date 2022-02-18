#!/usr/bin/env bash

find "$@" -type f -print0 | xargs -0 sed -i -r \
  -e 's/wmsingles\.settings/node_singles\.settings/g' \
  -e 's/isSingle:/is_single:/g' \
  -e "s/'isSingle'/'is_single'/g" \
  -e 's/"isSingle"/"is_single"/g' \
  -e 's/\[wmsingles:/\[node_singles:/g' \
  -e 's/wmsingles\.overview/node_singles\.overview/g' \
  -e 's/wmsingles\.content/node_singles\.content/g' \
  -e 's/WmSingles(Interface)?/NodeSingles\1/g' \
  -e 's/Drupal\\wmsingles/Drupal\\node_singles/g' \
  -e 's/administer wmsingles/administer node singles/g' \
  -e 's/access wmsingles overview/access node singles overview/g' \
  -e 's/wmsingles\.nodeform_subscriber/node_singles\.node_form\.subscriber/g' \
  -e 's/wmsingles\.nodetypeform_subscriber/node_singles\.node_type_form\.subscriber/g' \
  -e 's/wmsingles\.nodetypeupdate_subscriber/node_singles\.node_typeupdate_subscriber/g' \
  -e 's/wmsingles\.twig_extension/node_singles\.twig_extension/g' \
  -e 's/wmsingles/node_singles/g'

find "$@" -type f -iname "wmsingles.settings.yml" -exec rename 's/wmsingles/node_singles/' '{}' \;
