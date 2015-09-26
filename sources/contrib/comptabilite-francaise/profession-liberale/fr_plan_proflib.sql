--
-- PostgreSQL database dump
-- Version 2007-09-08 01:26
--

SET client_encoding = 'LATIN1';
SET check_function_bodies = false;
SET client_min_messages = warning;

SET search_path = public, pg_catalog;

--
-- Data for Name: tmp_pcmn; Type: TABLE DATA; Schema: public; Owner: phpcompta
--

COPY tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_country) FROM stdin;
1	Comptes de capitaux	0	FR
2	Comptes d'immobilisations	0	FR
21	Immobilisations corporelles	2	FR
3	Comptes de stocks et en-cours	0	FR
4	Comptes de tiers	0	FR
445	T.V.A. collectée 19,6% sur honoraires	4	FR
4456	T.V.A. déductible	4	FR
445661	T.V.A. déductible 19,6%	4456	FR
445662	T.V.A. déductible 5,5%	4456	FR
445663	T.V.A. déductible 2,1%	4456	FR
455	Apports et prélèvements de l'exploitant	4	FR
5	Comptes financiers	0	FR
512001	Banque 1	5	FR
512002	Banque 2	5	FR
53	Caisse	4	FR
58	Virements internes	5	FR
6	Comptes de charges	0	FR
62	Honoraires rétrocédés	6	FR
60	Achats	6	FR
641	Rémunération du personnel	6	FR
645	Charges sociales sur salaires (pat.+sal.)	6	FR
63	Taxe professionnelle et autres impôts	6	FR
610	Loyer et charges locatives	6	FR
613	Location de matériel et de mobilier	6	FR
615	T.F.S.E. Entretien et réparation	6	FR
621	T.F.S.E. Personnel intérimaire	6	FR
618	T.F.S.E. Petit outillage	6	FR
610001	T.F.S.E. Chauffage électricité	610	FR
622	T.F.S.E. Honoraires non rétrocédés	6	FR
616	T.F.S.E. Primes d'assurances	6	FR
625	Transports et déplacements	6	FR
647	Loi Madelin	6	FR
646	Charges sociales personnelles	6	FR
6257	Frais de réception, représentation et de congrès	625	FR
626	Frais divers de gestion : Frais postaux et télécommunications	6	FR
658001	Frais divers de gestion : Actes et contentieux	6	FR
628	Frais divers de gestion : Cotis. syndic. et prof.	6	FR
658	Frais divers de gestion : Autres	6	FR
66	Frais financiers	6	FR
635	Charges non déductibles : CSG	6	FR
7	Comptes de produits	0	FR
706	Honoraires	7	FR
708	Autres recettes diverses	7	FR
8	Comptes spéciaux	0	FR
9	Comptabilité analytique	0	FR
\.

--
-- PostgreSQL database dump complete
--
