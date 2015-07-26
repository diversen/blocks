RENAME TABLE `block_manip` TO `blocks`;

DELETE FROM `config` where id ='blocks';

DELETE FROM `config` where id ='blocks_unused';