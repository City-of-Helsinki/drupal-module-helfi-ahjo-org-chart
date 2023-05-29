# Drupal Helfi Ahjo Sote integration

Work in progress as design and final requirements are not completed.

This module syncs organization structure in a taxonomy tree under sote_section vocabulary.

Provides a paragraph component to list the org tree based on certain criteria (start of tree, excluding some types, depth) entered
during component editing.

Configuration at /admin/config/ahjo
- Base url of the API
- API key
- Root organisation id - from where to start/keep sync
- Max depth for sync

It has a cron hook that syncs at the set interval in config all the orgs under the configured root org id and max depth

The twig for the paragraph is here - https://github.com/City-of-Helsinki/drupal-hdbt/pull/651

## Instructions

1. Go to /admin/config/ahjo and fill in API url and API key.
2. On the Cron Configs fieldset, choose your desired interval for syncing.
Fill in Organisation ID for CoH. The Max Depth field represents the parameter
in the request that refers to the depth of the tree being retrieved. (e.g 9999 for entire tree
or 0003 for three levels below tree)
3. Use Sync Now to start the batch process that creates the taxonomy tree in Drupal.
4. The paragraph type 'Sote Section' is available to be referenced from a paragraph entity reference field.
5. Place the 'Sote Section' paragraph in a page and select from the Organization field the desired
organization from which the tree will start.
Max Depth field is used to specify how many levels below in the tree to be displayed.
Exclude by Type Id field allows you to exclude organizations by their type id from the display of the tree.
