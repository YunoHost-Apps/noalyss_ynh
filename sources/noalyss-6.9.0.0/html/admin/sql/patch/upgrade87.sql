BEGIN;
select comptaproc.fiche_attribut_synchro(fd_id) from fiche_def;

DROP TRIGGER t_jnt_fic_attr_ins ON jnt_fic_attr;

CREATE TRIGGER t_jnt_fic_attr_ins
  after INSERT
  ON jnt_fic_attr
  FOR EACH ROW
  EXECUTE PROCEDURE comptaproc.jnt_fic_attr_ins();

UPDATE VERSION SET VAL=88;


COMMIT;
