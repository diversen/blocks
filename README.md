### About

CosCMS Module for manipulating blocks. Blocks to be used 
are set in config/config.ini

Example from config.ini: 

    blocks_top[] = "/modules/system/blocks/system_admin_menu_top.inc"
    blocks[] = "/modules/content/blocks/content_tree_keep_state.inc"
    blocks_all = "blocks,blocks_sec,blocks_top"
 
The `blocks_all` defines all blocks.

If blocks_manip is installed you will now be able to manipulate the blocks
defined in `blocks_all`. This means you can drag the blocks up and down
using the jquerysort modules.

### Configuration

    ; how do we filter custom blocks
    blocks_filters[0] = "markdown"
    ; which blocks can we manipulate - these blocks should also be enabled 
    ; in config/config.ini
    ; the blocks_unused is used for newly created custom blocks
    blocks_blocks = "blocks,blocks_sec,blocks_unused"
    ; who can manipulate blogs
    blocks_allow = "admin"
    ; use markedit editor
    blocks_markedit = 1
    ; block manip sub modules
    blocks_modules[] = 'image'

# blocks
