begin;
-- View: vw_fiche_def

create index jnt_fic_att_value_fd_id_idx on jnt_fic_att_value(f_id);
create index jnt_fic_attr_fd_id_idx on jnt_fic_attr(fd_id);

update version set val=63;

commit;
