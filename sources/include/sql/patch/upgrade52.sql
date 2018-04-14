begin;
CREATE TABLE mod_payment (
    mp_id serial,
    mp_lib text NOT NULL,
    mp_jrn_def_id integer NOT NULL,
    mp_type character varying(3) NOT NULL,
    mp_fd_id bigint ,
    mp_qcode text
);
COMMENT ON TABLE mod_payment IS 'Contains the different media of payment and the corresponding ledger';

INSERT INTO mod_payment (mp_id, mp_lib, mp_jrn_def_id, mp_type, mp_fd_id, mp_qcode) VALUES (2, 'Caisse', 1, 'VEN', NULL, NULL);
INSERT INTO mod_payment (mp_id, mp_lib, mp_jrn_def_id, mp_type, mp_fd_id, mp_qcode) VALUES (4, 'Caisse', 1, 'ACH', NULL, NULL);
INSERT INTO mod_payment (mp_id, mp_lib, mp_jrn_def_id, mp_type, mp_fd_id, mp_qcode) VALUES (1, 'Paiement électronique', 1, 'VEN', NULL, NULL);
INSERT INTO mod_payment (mp_id, mp_lib, mp_jrn_def_id, mp_type, mp_fd_id, mp_qcode) VALUES (3, 'Par gérant ou administrateur', 2, 'ACH', NULL, NULL);

ALTER TABLE ONLY mod_payment
    ADD CONSTRAINT mod_payment_pkey PRIMARY KEY (mp_id);
ALTER TABLE ONLY mod_payment
    ADD CONSTRAINT mod_payment_mp_fd_id_fkey FOREIGN KEY (mp_fd_id) REFERENCES fiche_def(fd_id);
ALTER TABLE ONLY mod_payment
    ADD CONSTRAINT mod_payment_mp_jrn_def_id_fkey FOREIGN KEY (mp_jrn_def_id) REFERENCES jrn_def(jrn_def_id);
INSERT INTO document_type (dt_id, dt_value) VALUES (10, 'Note de frais');

insert into fiche_def_ref (frd_id,frd_text) values (25,'Compte Salarié / Administrateur');
insert into attr_min values (25,1);
insert into attr_min values(25,4);
insert into attr_min values (25,3);
insert into attr_min values(25,5);
insert into attr_min values (25,15);
insert into attr_min values(25,16);
insert into attr_min values(25,24);
insert into attr_min values(25,23);


update version set val=53;
commit;
