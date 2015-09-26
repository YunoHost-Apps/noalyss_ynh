--
-- PostgreSQL database dump
-- Version 2007/10/23 22:23
--

SET client_encoding = 'LATIN1';
SET check_function_bodies = false;
SET client_min_messages = warning;

SET search_path = public, pg_catalog;

--
-- Data for Name: tva_rate; Type: TABLE DATA; Schema: public; Owner: phpcompta
--

COPY tva_rate (tva_id, tva_label, tva_rate, tva_comment, tva_poste) FROM stdin;
101	TVAFranceNormal	0.196	TVA 19,6% - France métropolitaine - Taux normal	4456,445
102	TVAFranceRéduit	0.055	TVA 5,5% - France métropolitaine - Taux réduit	4456,445
103	TVAFranceSuperRéduit	0.021	TVA 2,1% - France métropolitaine - Taux super réduit	4456,445
104	TVAFranceImmos	0.196	TVA 19,6% - France métropolitaine - Taux immobilisations	4456,445
105	TVAFranceAnciens	0	TVA x% - France métropolitaine - Taux anciens	4456,445
201	TVADomNormal	0.085	TVA 8,5%  - DOM - Taux normal	4456,445
202	TVADomNPR	0.085	TVA 8,5% - DOM - Taux normal NPR	4456,445
203	TVADomRéduit	0.021	TVA 2,1% - DOM - Taux réduit	4456,445
204	TVADom-I	0.0175	TVA 1,75% - DOM - Taux I	4456,445
205	TVADomPresse	0.0105	TVA 1,05% - DOM - Taux publications de presse	4456,445
206	TVADomOctroi	0	TVA x% - DOM - Taux octroi de mer	4456,445
207	TVADomImmos	0	TVA x% - DOM - Taux immobilisations	4456,445
301	TVACorse-I	0.13	TVA 13% - Corse - Taux I	4456,445
302	TVACorse-II	0.08	TVA 8% - Corse - Taux II	4456,445
303	TVACorse-III	0.021	TVA 2,1% - Corse - Taux III	4456,445
304	TVACorse-IV	0.009	TVA 0,9% - Corse - Taux IV	4456,445
305	TVACorseImmos	0	TVA x% - Corse - Taux immobilisations	4456,445
401	TVAacquisIntracom	0	TVA x% - Acquisitions intracommunautaires/Pays	4456,445
402	TVAacquisIntracomImmos	0	TVA x% - Acquisitions intracommunautaires immobilisations/Pays	4456,445
501	TVAfranchise	0	TVA x% - Non imposable : Achats en franchise	
502	TVAexport	0	TVA x% - Non imposable : Exports hors CE/Pays	
503	TVAautres	0	TVA x% - Non imposable : Autres opérations	
504	TVAlivrIntracom	0	TVA x% - Non imposable : Livraisons intracommunautaires/Pays	
\.

--
-- PostgreSQL database dump complete
--
