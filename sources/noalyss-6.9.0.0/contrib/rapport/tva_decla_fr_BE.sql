--
-- PostgreSQL database dump
--

SET client_encoding = 'LATIN1';

SET search_path = public, pg_catalog;

delete from form where fo_fr_id=3000000;
delete from formdef where fr_id=3000000;

INSERT INTO formdef (fr_id, fr_label) VALUES (3000000, 'TVA déclaration');
--
-- Data for TOC entry 2 (OID 315304)
-- Name: formdef; Type: TABLE DATA; Schema: public; Owner: dany
--
--

INSERT INTO form VALUES (3000398, 3000000, 1, 'Prestation [ case 03 ]', '[700%]-[7000005]');
INSERT INTO form VALUES (3000399, 3000000, 2, 'Prestation intra [ case 47 ]', '[7000005]');
INSERT INTO form VALUES (3000400, 3000000, 3, 'Tva due   [case 54]', '[4513]+[4512]+[4511] FROM=01.2005');
INSERT INTO form VALUES (3000401, 3000000, 4, 'Marchandises, matière première et auxiliaire [case 81 ]', '[60%]');
INSERT INTO form VALUES (3000402, 3000000, 7, 'Service et bien divers [case 82]', '[61%]');
INSERT INTO form VALUES (3000403, 3000000, 8, 'bien d''invest [ case 83 ]', '[2400%]');
INSERT INTO form VALUES (3000404, 3000000, 9, 'TVA déductible [ case 59 ]', 'abs([4117]-[411%])');
INSERT INTO form VALUES (3000405, 3000000, 8, 'TVA non ded -> voiture', '[610022]*0.21/2');
INSERT INTO form VALUES (3000406, 3000000, 9, 'Acompte TVA', '[4117]');

