# Drupal Helfi Ahjo Sote integration

Work in progress as design and final requirements are not completed.

This module syncs organization structure in a taxonomy tree under sote_section vocabulary.

Provides a paragraph component to list the org tree based on certain criteria (start of tree, excluding some types, depth) entered during component editing.

Configuration at /admin/config/ahjo
- Base url of the API
- API key
- Root organisation id - from where to start/keep sync
- Max depth for sync

It has a cron hook that syncs at the set interval in config all the orgs under the configured root org id and max depth

The twig for the paragraph is here - https://github.com/City-of-Helsinki/drupal-hdbt/pull/651

