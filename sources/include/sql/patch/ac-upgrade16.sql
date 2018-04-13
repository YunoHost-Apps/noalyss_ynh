begin;
ALTER TABLE public.ac_dossier ADD dos_email int4 NULL DEFAULT -1;
comment on column public.ac_dossier.dos_email is 'Max emails per day : 0 none , -1 unlimited or  max value';

CREATE TABLE public.dossier_sent_email (
	id serial primary key,
	de_date varchar(8) NOT NULL,
	de_sent_email int4 NOT NULL,
	dos_id int4 NOT NULL
);
comment on table public.dossier_sent_email is 'Count the sent email by folder';
comment on column public.dossier_sent_email.id is 'primary key';
comment on column public.dossier_sent_email.de_date is 'Date YYYYMMDD';
comment on column public.dossier_sent_email.de_sent_email  is 'Number of sent emails';
comment on column public.dossier_sent_email.dos_id is 'Link to ac_dossier';

alter table public.dossier_sent_email add constraint de_ac_dossier_fk foreign key (dos_id) references ac_dossier (dos_id ) on update cascade on delete cascade;
alter table public.dossier_sent_email add constraint de_date_dos_id_ux unique (de_date,dos_id);
select upgrade_repo(17);
commit;
