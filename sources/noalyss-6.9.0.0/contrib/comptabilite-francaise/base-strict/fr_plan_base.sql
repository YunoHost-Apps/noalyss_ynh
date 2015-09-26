--
-- PostgreSQL database dump
-- Version 2007-09-08 01:10
--

SET client_encoding = 'LATIN1';
SET check_function_bodies = false;
SET client_min_messages = warning;

SET search_path = public, pg_catalog;

--
-- Data for Name: tmp_pcmn; Type: TABLE DATA; Schema: public; Owner: phpcompta
--

COPY tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_country) FROM stdin;
1	comptes de capitaux	0	FR
10	capital et réserves	1	FR
104	Primes liées au capital social	10	FR
106	Réserves	10	FR
1062	Réserves indisponibles	106	FR
107	Ecart d'équivalence	10	FR
109	Actionnaires : Capital souscrit - non appelé	10	FR
11	report a nouveau (solde créditeur ou débiteur)	1	FR
13	subventions d'investissement	1	FR
131	Subventions d'équipement	13	FR
138	Autres subventions d'investissement (même ventilation que celle du compte 131)	13	FR
139	Subventions d'investissement inscrites au compte de résultat	13	FR
1391	Subventions d'équipement	139	FR
1398	Autres subventions d'investissement (même ventilation que celle du compte 1391)	139	FR
14	provisions reglementees	1	FR
142	Provisions réglementées relatives aux immobilisations	14	FR
143	Provisions réglementées relatives aux stocks	14	FR
144	Provisions réglementées relatives aux autres éléments de l'actif	14	FR
151	Provisions pour risques	1	FR
153	Provisions pour pensions et obligations similaires	1	FR
155	Provisions pour impôts	1	FR
156	Provisions pour renouvellement des immobilisations (entreprises concessionnaires)	1	FR
157	Provisions pour charges à répartir sur plusieurs exercices	1	FR
158	Autres provisions pour charges	1	FR
161	Emprunts obligataires convertibles	1	FR
163	Autres emprunts obligataires	1	FR
164	Emprunts auprès des établissements de crédit	1	FR
165	Dépôts et cautionnements reçus	1	FR
166	Participation des salariés aux résultats	1	FR
167	Emprunts et dettes assortis de conditions particulières	1	FR
1671	Emissions de titres participatifs	167	FR
1674	Avances conditionnées de l'Etat	167	FR
1675	Emprunts participatifs	167	FR
168	Autres emprunts et dettes assimilées	1	FR
169	Primes de remboursement des obligations	1	FR
17	dettes rattachées a des participations	1	FR
171	Dettes rattachées à des participations (groupe)	17	FR
174	Dettes rattachées à des participations (hors groupe)	17	FR
178	Dettes rattachées à des sociétés en participation	17	FR
18	comptes de liaison des établissements et societes en participation	1	FR
2	comptes d'immobilisations	0	FR
203	Frais de recherche et de développement	2	FR
205	Concessions et droits similaires, brevets, licences, marques, procédés, logiciels, droits et valeurs similaires	2	FR
211	Terrains	2	FR
2111	Terrains nus	211	FR
2112	Terrains aménagés	211	FR
2113	Sous-sols et sur-sols	211	FR
2114	Terrains de gisement	211	FR
2115	Terrains bâtis	211	FR
2116	Compte d'ordre sur immobilisations (art. 6 du décret n° 78-737 du 11 juillet 1978)	211	FR
212	Agencements et aménagements de terrains (même ventilation que celle du compte 211)	2	FR
213	Constructions	2	FR
2131	Bâtiments	213	FR
2135	Installations générales - agencements - aménagements des constructions (même ventilation que celle du compte 2131)	213	FR
2138	Ouvrages d'infrastructure	213	FR
214	Constructions sur sol d'autrui (même ventilation que celle du compte 213)	2	FR
215	Installations techniques, matériels et outillage industriels	2	FR
2151	Installations complexes spécialisées	215	FR
2153	Installations à caractère spécifique	215	FR
2154	Matériel industriel	215	FR
2155	Outillage industriel	215	FR
2157	Agencements et aménagements du matériel et outillage industriels	215	FR
218	Autres immobilisations corporelles	2	FR
2181	Installations générales, agencements, aménagements divers	218	FR
2182	Matériel de transport	218	FR
2183	Matériel de bureau et matériel informatique	218	FR
2184	Mobilier	218	FR
2185	Cheptel	218	FR
2186	Emballages récupérables	218	FR
22	immobilisations mises en concession	2	FR
231	Immobilisations corporelles en cours	2	FR
232	Immobilisations incorporelles en cours	2	FR
237	Avances et acomptes versés sur immobilisations incorporelles	2	FR
238	Avances et acomptes versés sur commandes d'immobilisations corporelles	2	FR
25	Parts dans des entreprises liées et créances sur des entreprises liées	2	FR
26	Participations et créances rattachées à des participations	2	FR
261	Titres de participation	26	FR
266	Autres formes de participation	26	FR
267	Créances rattachées à des participations	26	FR
268	Créances rattachées à des sociétés en participation	26	FR
269	Versements restant à effectuer sur titres de participation non libérés	26	FR
271	Titres immobilisés autres que les titres immobilisés de l'activité de portefeuille (droit de propriété)	2	FR
272	Titres immobilisés (droit de créance)	2	FR
273	Titres immobilisés de l'activité de portefeuille	2	FR
274	Prêts	2	FR
275	Dépôts et cautionnements versés	2	FR
276	Autres créances immobilisées	2	FR
277	(Actions propres ou parts propres)	2	FR
279	Versements restant à effectuer sur titres immobilisés non libérés	2	FR
28	amortissements des immobilisations	2	FR
2801	Frais d'établissement (même ventilation que celle du compte 201)	28	FR
2803	Frais de recherche et de développement	28	FR
2805	Concessions et droits similaires, brevets, licences, logiciels, droits et valeurs similaires	28	FR
2807	Fonds commercial	28	FR
2808	Autres immobilisations incorporelles	28	FR
2811	Terrains de gisement	28	FR
2812	Agencements, aménagements de terrains (même ventilation que celle du compte 212)	2	FR
2813	Constructions (même ventilation que celle du compte 213)	2	FR
2814	Constructions sur sol d'autrui (même ventilation que celle du compte 214)	2	FR
2815	Installations, matériel et outillage industriels (même ventilation que celle du compte 215)	2	FR
2818	Autres immobilisations corporelles (même ventilation que celle du compte 218)	2	FR
282	Amortissements des immobilisations mises en concession	28	FR
29	provisions pour dépréciation des immobilisations	2	FR
2905	Marques, procédés, droits et valeurs similaires	29	FR
2906	Droit au bail	29	FR
2907	Fonds commercial	29	FR
2908	Autres immobilisations incorporelles	29	FR
2911	Terrains (autres que terrains de gisement)	29	FR
292	Provisions pour dépréciation des immobilisations mises en concession	29	FR
293	Provisions pour dépréciation des immobilisations en cours	29	FR
2931	Immobilisations corporelles en cours	293	FR
2932	Immobilisations incorporelles en cours	293	FR
296	Provisions pour dépréciation des participations et créances rattachées à des participations	29	FR
2961	Titres de participation	296	FR
2966	Autres formes de participation	296	FR
2967	Créances rattachées à des participations (même ventilation que celle du compte 267)	26	FR
2968	Créances rattachées à des sociétés en participation (même ventilation que celle du compte 268)	26	FR
2971	Titres immobilisés autres que les titres immobilisés de l'activité de portefeuille -droit de propriété (même ventilation que celle du compte 271)	2	FR
2972	Titres immobilisés - droit de créance (même ventilation que celle du compte 272)	2	FR
2973	Titres immobilisés de l'activité de portefeuille	29	FR
2974	Prêts (même ventilation que celle du compte 274)	2	FR
2975	Dépôts et cautionnements versés (même ventilation que celle du compte 275)	2	FR
2976	Autres créances immobilisées (même ventilation que celle du compte 276)	2	FR
3	comptes de stocks et en cours	0	FR
321	Matières consommables	3	FR
322	Fournitures consommables	3	FR
326	Emballages	3	FR
331	Produits en cours	3	FR
335	Travaux en cours	3	FR
341	Etudes en cours	3	FR
345	Prestations de services en cours	3	FR
351	Produits intermédiaires	3	FR
355	Produits finis	3	FR
358	Produits résiduels (ou matières de récupération)	3	FR
36	(compte à ouvrir, le cas échéant, sous l'intitulé " stocks provenant d'immobilisations ")	3	FR
38	(lorsque l'entité tient un inventaire permanent en comptabilité générale, le compte 38 peut être utilisé pour comptabiliser les stocks en voie d'acheminement, mis en dépôt ou donnés en consignation)	3	FR
39	provisions pour dépréciation des stocks et en-cours	3	FR
4	comptes de tiers	0	FR
40	fournisseurs et comptes rattaches	4	FR
401	Fournisseurs	40	FR
403	Fournisseurs - Effets à payer	40	FR
404	Fournisseurs d'immobilisations	40	FR
405	Fournisseurs d'immobilisations - Effets à payer	40	FR
408	Fournisseurs - Factures non parvenues	40	FR
4091	Fournisseurs - Avances et acomptes versés sur commandes	40	FR
4096	Fournisseurs - Créances pour emballages et matériel à rendre	40	FR
4097	Fournisseurs - Autres avoirs	40	FR
4098	Rabais, remises, ristournes à obtenir et autres avoirs non encore reçus	40	FR
41	clients et comptes rattaches	4	FR
411	Clients	41	FR
413	Clients - Effets à recevoir	41	FR
416	Clients douteux ou litigieux	41	FR
417	" Créances " sur travaux non encore facturables	41	FR
418	Clients - Produits non encore facturés	41	FR
4191	Clients - Avances et acomptes reçus sur commandes	41	FR
4196	Clients - Dettes sur emballages et matériels consignés	41	FR
4197	Clients - Autres avoirs	41	FR
4198	Rabais, remises, ristournes à accorder et autres avoirs à établir	41	FR
42	Personnel et comptes rattaches	4	FR
422	Comités d'entreprises, d'établissement,...	42	FR
424	Participation des salariés aux résultats	42	FR
425	Personnel - Avances et acomptes	42	FR
426	Personnel - Dépôts	42	FR
427	Personnel - Oppositions	42	FR
431	Sécurité sociale	4	FR
437	Autres organismes sociaux	4	FR
438	Organismes sociaux - Charges à payer et produits à recevoir	4	FR
44	État et autres collectivités publiques	4	FR
441	État - Subventions à recevoir	44	FR
442	Etat - Impôts et taxes recouvrables sur des tiers	44	FR
443	Opérations particulières avec l'Etat les collectivités publiques, les organismes internationaux	44	FR
4431	Créances sur l'Etat résultant de la suppression de la règle du décalage d'un mois en matière de T.V.A.	443	FR
4438	Intérêts courus sur créances figurant au 4431	443	FR
4452	T.V.A. due intracommunautaire	44	FR
4455	Taxes sur le chiffre d'affaires à décaisser	44	FR
4456	Taxes sur le chiffre d'affaires déductibles	44	FR
4457	Taxes sur le chiffre d'affaires collectées par l'entreprise	44	FR
4458	Taxes sur le chiffre d'affaires à régulariser ou en attente	44	FR
446	Obligations cautionnées	44	FR
448	Etat - Charges à payer et produits à recevoir	44	FR
451	Groupe	4	FR
456	Associés - Opérations sur le capital	4	FR
457	Associés - Dividendes à payer	4	FR
458	Associés - Opérations faites en commun et en G.I.E.	4	FR
471	Comptes d'attente	4	FR
472	Comptes d'attente	4	FR
473	Comptes d'attente	4	FR
474	Comptes d'attente	4	FR
475	Comptes d'attente	4	FR
476	Différence de conversion - Actif	4	FR
477	Différences de conversion - Passif	4	FR
478	Autres comptes transitoires	4	FR
48	comptes de régularisation	4	FR
4811	Charges différées	48	FR
4812	Frais d'acquisition des immobilisations	48	FR
4816	Frais d'émission des emprunts	48	FR
4818	Charges à étaler	48	FR
488	Comptes de répartition périodique des charges et des produits	48	FR
49	provisions pour dépréciation des comptes de tiers	4	FR
495	Provisions pour dépréciation des comptes du groupe et des associés	49	FR
4951	Comptes du groupe	495	FR
4955	Comptes courants des associés	495	FR
4958	Opérations faites en commun et en G.I.E.	495	FR
5	comptes financiers	0	FR
501	Parts dans des entreprises liées	5	FR
502	Actions propres	5	FR
503	Actions	5	FR
504	Autres titres conférant un droit de propriété	5	FR
505	Obligations et bons émis par la société et rachetés par elle	5	FR
506	Obligations	5	FR
507	Bons du Trésor et bons de caisse à court terme	5	FR
508	Autres valeurs mobilières de placement et autres créances assimilées	5	FR
509	Versements restant à effectuer sur valeurs mobilières de placement non libérées	5	FR
511	Valeurs à l'encaissement	5	FR
512	Banques	5	FR
514	Chèques postaux	5	FR
515	" Caisses " du Trésor et des établissements publics	5	FR
516	Sociétés de bourse	5	FR
517	Autres organismes financiers	5	FR
518	Intérêts courus	5	FR
519	Concours bancaires courants	5	FR
52	Instruments de trésorerie	5	FR
59	provisions pour dépréciation des comptes financiers	5	FR
5903	Actions	59	FR
5904	Autres titres conférant un droit de propriété	59	FR
5906	Obligations	59	FR
5908	Autres valeurs mobilières de placement et créances assimilées	59	FR
6	comptes de charges	0	FR
601	Achats stockés - Matières premières (et fournitures)	6	FR
602	Achats stockés - Autres approvisionnements	6	FR
6021	Matières consommables	602	FR
6022	Fournitures consommables	602	FR
6026	Emballages	602	FR
604	Achats d'études et prestations de services	6	FR
605	Achats de matériel, équipements et travaux	6	FR
606	Achats non stockés de matière et fournitures	6	FR
607	Achats de marchandises	6	FR
608	(Compte réservé, le cas échéant, à la récapitulation des frais accessoires incorporés aux achats)	6	FR
609	Rabais, remises et ristournes obtenus sur achats	6	FR
6031	Variation des stocks de matières premières (et fournitures)	6	FR
6032	Variation des stocks des autres approvisionnements	6	FR
6037	Variation des stocks de marchandises	6	FR
61	autres charges externes - Services extérieurs	6	FR
611	Sous-traitance générale	61	FR
612	Redevances de crédit-bail	61	FR
6122	Crédit-bail mobilier	612	FR
6125	Crédit-bail immobilier	612	FR
613	Locations	61	FR
614	Charges locatives et de copropriété	61	FR
615	Entretien et réparations	61	FR
616	Primes d'assurances	61	FR
617	Etudes et recherches	61	FR
618	Divers	61	FR
619	Rabais, remises et ristournes obtenus sur services extérieurs	61	FR
62	autres charges externes - Autres services extérieurs	6	FR
621	Personnel extérieur à l'entreprise	62	FR
622	Rémunérations d'intermédiaires et honoraires	62	FR
623	Publicité, publications, relations publiques	62	FR
624	Transports de biens et transports collectifs du personnel	62	FR
625	Déplacements, missions et réceptions	62	FR
626	Frais postaux et de télécommunications	62	FR
627	Services bancaires et assimilés	62	FR
628	Divers	62	FR
629	Rabais, remises et ristournes obtenus sur autres services extérieurs	62	FR
631	Impôts, taxes et versements assimilés sur rémunérations (administrations des impôts)	6	FR
633	Impôts, taxes et versements assimilés sur rémunérations (autres organismes)	6	FR
635	Autres impôts, taxes et versements assimilés (administrations des impôts)	6	FR
637	Autres impôts, taxes et versements assimilés (autres organismes)	6	FR
64	Charges de personnel	6	FR
647	Autres charges sociales	64	FR
648	Autres charges de personnel	64	FR
651	Redevances pour concessions, brevets, licences, marques, procédés, logiciels, droits et valeurs similaires	6	FR
653	Jetons de présence	6	FR
654	Pertes sur créances irrécouvrables	6	FR
655	Quotes-parts de résultat sur opérations faites en commun	6	FR
658	Charges diverses de gestion courante	6	FR
661	Charges d'intérêts	6	FR
664	Pertes sur créances liées à des participations	6	FR
665	Escomptes accordés	6	FR
666	Pertes de change	6	FR
667	Charges nettes sur cessions de valeurs mobilières de placement	6	FR
668	Autres charges financières	6	FR
671	Charges exceptionnelles sur opérations de gestion	6	FR
672	(Compte à la disposition des entités pour enregistrer, en cours d'exercice, les charges sur exercices antérieurs)	6	FR
675	Valeurs comptables des éléments d'actif cédés	6	FR
678	Autres charges exceptionnelles	6	FR
68	Dotations aux amortissements et aux provisions	6	FR
6811	Dotations aux amortissements sur immobilisations incorporelles et corporelles	68	FR
6812	Dotations aux amortissements des charges d'exploitation à répartir	68	FR
6815	Dotations aux provisions pour risques et charges d'exploitation	68	FR
6816	Dotations aux provisions pour dépréciation des immobilisations incorporelles et corporelles	68	FR
6817	Dotations aux provisions pour dépréciation des actifs circulants	68	FR
6861	Dotations aux amortissements des primes de remboursement des obligations	68	FR
6865	Dotations aux provisions pour risques et charges financiers	68	FR
6866	Dotations aux provisions pour dépréciation des éléments financiers	68	FR
6868	Autres dotations	68	FR
6871	Dotations aux amortissements exceptionnels des immobilisations	68	FR
6872	Dotations aux provisions réglementées (immobilisations)	68	FR
6873	Dotations aux provisions réglementées (stocks)	68	FR
6874	Dotations aux autres provisions réglementées	68	FR
6875	Dotations aux provisions pour risques et charges exceptionnels	68	FR
6876	Dotations aux provisions pour dépréciations exceptionnelles	68	FR
69	participation des salaries - impôts sur les benefices et assimiles	6	FR
696	Suppléments d'impôt sur les sociétés liés aux distributions	69	FR
698	Intégration fiscale	69	FR
6981	Intégration fiscale - Charges	698	FR
6989	Intégration fiscale - Produits	698	FR
7	comptes de produits	0	FR
70	ventes de produits fabriques, prestations de services, marchandises	7	FR
702	Ventes de produits intermédiaires	70	FR
703	Ventes de produits résiduels	70	FR
704	Travaux	70	FR
705	Etudes	70	FR
71	production stockée (ou déstockage)	7	FR
7133	Variation des en-cours de production de biens	71	FR
7134	Variation des en-cours de production de services	71	FR
7135	Variation des stocks de produits	71	FR
721	Immobilisations incorporelles	7	FR
722	Immobilisations corporelles	7	FR
731	Produits nets partiels sur opérations en cours (à subdiviser par opération)	7	FR
739	Produits nets partiels sur opérations terminées	7	FR
751	Redevances pour concessions, brevets, licences, marques, procédés, logiciels, droits et valeurs similaires	7	FR
752	Revenus des immeubles non affectés à des activités professionnelles	7	FR
758	Produits divers de gestion courante	7	FR
761	Produits de participations	7	FR
762	Produits des autres immobilisations financières	7	FR
763	Revenus des autres créances	7	FR
764	Revenus des valeurs mobilières de placement	7	FR
765	Escomptes obtenus	7	FR
766	Gains de change	7	FR
767	Produits nets sur cessions de valeurs mobilières de placement	7	FR
768	Autres produits financiers	7	FR
771	Produits exceptionnels sur opérations de gestion	7	FR
772	(Compte à la disposition des entités pour enregistrer, en cours d'exercice, les produits sur exercices antérieurs)	7	FR
775	Produits des cessions d'éléments d'actif	7	FR
777	Quote-part des subventions d'investissement virée au résultat de l'exercice	7	FR
778	Autres produits exceptionnels	7	FR
78	Reprises sur amortissements et provisions	7	FR
7811	Reprises sur amortissements des immobilisations incorporelles et corporelles	78	FR
7815	Reprises sur provisions pour risques et charges d'exploitation	78	FR
7816	Reprises sur provisions pour dépréciation des immobilisations incorporelles et corporelles	78	FR
7817	Reprises sur provisions pour dépréciation des actifs circulants	78	FR
7865	Reprises sur provisions pour risques et charges financiers	78	FR
7866	Reprises sur provisions pour dépréciation des éléments financiers	78	FR
7872	Reprises sur provisions réglementées (immobilisations)	78	FR
7873	Reprises sur provisions réglementées (stocks)	78	FR
7874	Reprises sur autres provisions réglementées	78	FR
7875	Reprises sur provisions pour risques et charges exceptionnels	78	FR
7876	Reprises sur provisions pour dépréciations exceptionnelles	78	FR
791	Transferts de charges d'exploitation	7	FR
796	Transferts de charges financières	7	FR
797	Transferts de charges exceptionnelles	7	FR
8	Comptes spéciaux	0	FR
80	Engagements hors bilan	8	FR
801	Engagements donnés par l'entité	80	FR
8011	Avals, cautions, garanties	801	FR
8014	Effets circulant sous l'endos de l'entité	801	FR
8016	Redevances crédit-bail restant à courir	801	FR
80161	Crédit-bail mobilier	8016	FR
80165	Crédit-bail immobilier	8016	FR
8018	Autres engagements donnés	801	FR
802	Engagements reçus par l'entité	80	FR
8021	Avals, cautions, garanties	802	FR
8024	Créances escomptées non échues	802	FR
8026	Engagements reçus pour utilisation en crédit-bail	802	FR
80261	Crédit-bail mobilier	8026	FR
80265	Crédit-bail immobilier	8026	FR
8028	Autres engagements reçus	802	FR
809	Contrepartie des engagements	80	FR
8091	Contrepartie 801	809	FR
8092	Contrepartie 802	809	FR
88	Résultat en instance d'affectation	8	FR
89	Bilan	8	FR
890	Bilan d'ouverture	89	FR
891	Bilan de clôture	89	FR
9	Comptes analytiques	0	FR
\.

--
-- PostgreSQL database dump complete
--
