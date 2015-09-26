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
1011	Capital souscrit - non appelé	10	FR
1012	Capital souscrit - appelé, non versé	10	FR
1013	Capital souscrit - appelé, versé	10	FR
10131	Capital non amorti	1013	FR
10132	Capital amorti	1013	FR
1018	Capital souscrit soumis à des réglementations particulières	10	FR
104	Primes liées au capital social	10	FR
1041	Primes d'émission	104	FR
1042	Primes de fusion	104	FR
1043	Primes d'apport	104	FR
1044	Primes de conversion d'obligations en actions	104	FR
1045	Bons de souscription d'actions	104	FR
1051	Réserve spéciale de réévaluation	10	FR
1052	Ecart de réévaluation libre	10	FR
1053	Réserve de réévaluation	10	FR
1055	Ecarts de rééavaluation (autres opérations légales)	10	FR
1057	Autres écarts de réévaluation en France	10	FR
1058	Autres écarts de réévaluation à l'Etranger	10	FR
106	Réserves	10	FR
10611	Réserve légale proprement dite	106	FR
10612	Plus-values nettes à long terme	106	FR
1062	Réserves indisponibles	106	FR
10641	Plus-values nettes à long terme	106	FR
10643	Réserves consécutives à l'octroi de subventions d'investissement	106	FR
10648	Autres réserves réglementées	106	FR
10681	Réserve de propre assureur	106	FR
10688	Réserves diverses	106	FR
107	Ecart d'équivalence	10	FR
109	Actionnaires : Capital souscrit - non appelé	10	FR
11	report a nouveau (solde créditeur ou débiteur)	1	FR
110	Report à nouveau (solde créditeur)	11	FR
119	Report à nouveau (solde débiteur)	11	FR
120	Résultat de l'exercice (bénéfice)	1	FR
129	Résultat de l'exercice (perte)	1	FR
13	subventions d'investissement	1	FR
131	Subventions d'équipement	13	FR
1311	Etat	131	FR
1312	Régions	131	FR
1313	Départements	131	FR
1314	Communes	131	FR
1315	Collectivités publiques	131	FR
1316	Entreprises publiques	131	FR
1317	Entreprises et organismes privés	131	FR
1318	Autres	131	FR
138	Autres subventions d'investissement (même ventilation que celle du compte 131)	13	FR
139	Subventions d'investissement inscrites au compte de résultat	13	FR
1391	Subventions d'équipement	139	FR
13911	Etat	1391	FR
13912	Régions	1391	FR
13913	Départements	1391	FR
13914	Communes	1391	FR
13915	Collectivités publiques	1391	FR
13916	Entreprises publiques	1391	FR
13917	Entreprises et organismes privés	1391	FR
13918	Autres	1391	FR
1398	Autres subventions d'investissement (même ventilation que celle du compte 1391)	139	FR
14	provisions reglementees	1	FR
142	Provisions réglementées relatives aux immobilisations	14	FR
1423	Provisions pour reconstitution des gisements miniers et pétroliers	142	FR
1424	Provisions pour investissement (participation des salariés)	142	FR
143	Provisions réglementées relatives aux stocks	14	FR
1431	Hausse des prix	143	FR
1432	Fluctuation des cours	143	FR
144	Provisions réglementées relatives aux autres éléments de l'actif	14	FR
151	Provisions pour risques	1	FR
1511	Provisions pour litiges	151	FR
1512	Provisions pour garanties données aux clients	151	FR
1513	Provisions pour pertes sur marchés à terme	151	FR
1514	Provisions pour amendes et pénalités	151	FR
1515	Provisions pour pertes de change	151	FR
1518	Autres provisions pour risques	151	FR
153	Provisions pour pensions et obligations similaires	1	FR
155	Provisions pour impôts	1	FR
156	Provisions pour renouvellement des immobilisations (entreprises concessionnaires)	1	FR
157	Provisions pour charges à répartir sur plusieurs exercices	1	FR
1572	Provisions pour grosses réparations	157	FR
158	Autres provisions pour charges	1	FR
1582	Provisions pour charges sociales et fiscales sur congés à payer	158	FR
161	Emprunts obligataires convertibles	1	FR
163	Autres emprunts obligataires	1	FR
164	Emprunts auprès des établissements de crédit	1	FR
165	Dépôts et cautionnements reçus	1	FR
1651	Dépôts	165	FR
1655	Cautionnements	165	FR
166	Participation des salariés aux résultats	1	FR
1661	Comptes bloqués	166	FR
1662	Fonds de participation	166	FR
167	Emprunts et dettes assortis de conditions particulières	1	FR
1671	Emissions de titres participatifs	167	FR
1674	Avances conditionnées de l'Etat	167	FR
1675	Emprunts participatifs	167	FR
168	Autres emprunts et dettes assimilées	1	FR
1681	Autres emprunts	168	FR
1685	Rentes viagères capitalisées	168	FR
1687	Autres dettes	168	FR
1688	Intérêts courus	168	FR
16881	Sur emprunts obligataires convertibles	168	FR
16883	Sur autres emprunts obligataires	168	FR
16884	Sur emprunts auprès des établissements de crédit	168	FR
16885	Sur dépôts et cautionnements reçus	168	FR
16886	Sur participation des salariés aux résultats	168	FR
16887	Sur emprunts et dettes assortis de conditions particulières	168	FR
16888	Sur autres emprunts et dettes assimilées	168	FR
169	Primes de remboursement des obligations	1	FR
17	dettes rattachées a des participations	1	FR
171	Dettes rattachées à des participations (groupe)	17	FR
174	Dettes rattachées à des participations (hors groupe)	17	FR
178	Dettes rattachées à des sociétés en participation	17	FR
1781	Principal	178	FR
1788	Intérêts courus	178	FR
18	comptes de liaison des établissements et societes en participation	1	FR
181	Comptes de liaison des établissements	18	FR
186	Biens et prestations de services échangés entre établissements (charges)	18	FR
187	Biens et prestations de services échangés entre établissements (produits)	18	FR
188	Comptes de liaison des sociétés en participation	18	FR
2	comptes d'immobilisations	0	FR
2011	Frais de constitution	2	FR
2012	Frais de premier établissement	2	FR
20121	Frais de prospection	2	FR
20122	Frais de publicité	2	FR
2013	Frais d'augmentation de capital et d'opérations diverses (fusions, scissions, transformations)	2	FR
203	Frais de recherche et de développement	2	FR
205	Concessions et droits similaires, brevets, licences, marques, procédés, logiciels, droits et valeurs similaires	2	FR
211	Terrains	2	FR
2111	Terrains nus	211	FR
2112	Terrains aménagés	211	FR
2113	Sous-sols et sur-sols	211	FR
2114	Terrains de gisement	211	FR
21141	Carrières	2114	FR
2115	Terrains bâtis	211	FR
21151	Ensembles immobiliers industriels (A, B...)	2115	FR
21155	Ensembles immobiliers administratifs et commerciaux (A, B...)	2115	FR
21158	Autres ensembles immobiliers	2115	FR
211581	affectés aux opérations professionnelles (A, B...)	21158	FR
211588	affectés aux opérations non professionnelles (A, B...)	21158	FR
2116	Compte d'ordre sur immobilisations (art. 6 du décret n° 78-737 du 11 juillet 1978)	211	FR
212	Agencements et aménagements de terrains (même ventilation que celle du compte 211)	2	FR
213	Constructions	2	FR
2131	Bâtiments	213	FR
21311	Ensembles immobiliers industriels (A, B...)	2131	FR
21315	Ensembles immobiliers administratifs et commerciaux (A, B...)	2131	FR
21318	Autres ensembles immobiliers	2131	FR
213181	affectés aux opérations professionnelles (A, B...)	21318	FR
213188	affectés aux opérations non professionnelles (A, B...)	21318	FR
2135	Installations générales - agencements - aménagements des constructions (même ventilation que celle du compte 2131)	213	FR
2138	Ouvrages d'infrastructure	213	FR
21381	Voies de terre	2138	FR
21382	Voies de fer	2138	FR
21383	Voies d'eau	2138	FR
21384	Barrages	2138	FR
21385	Pistes d'aérodromes	2138	FR
214	Constructions sur sol d'autrui (même ventilation que celle du compte 213)	2	FR
215	Installations techniques, matériels et outillage industriels	2	FR
2151	Installations complexes spécialisées	215	FR
21511	sur sol propre	2151	FR
21514	sur sol d'autrui	2151	FR
2153	Installations à caractère spécifique	215	FR
21531	sur sol propre	2153	FR
21534	sur sol d'autrui	2153	FR
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
2312	Terrains	231	FR
2313	Constructions	231	FR
2315	Installations techniques, matériel et outillage industriels	231	FR
2318	Autres immobilisations corporelles	231	FR
232	Immobilisations incorporelles en cours	2	FR
237	Avances et acomptes versés sur immobilisations incorporelles	2	FR
238	Avances et acomptes versés sur commandes d'immobilisations corporelles	2	FR
2382	Terrains	238	FR
2383	Constructions	238	FR
2385	Installations techniques, matériel et outillage industriels	238	FR
2388	Autres immobilisations corporelles	238	FR
25	Parts dans des entreprises liées et créances sur des entreprises liées	2	FR
26	Participations et créances rattachées à des participations	2	FR
261	Titres de participation	26	FR
2611	Actions	261	FR
2618	Autres titres	261	FR
266	Autres formes de participation	26	FR
267	Créances rattachées à des participations	26	FR
2671	Créances rattachées à des participations (groupe)	267	FR
2674	Créances rattachées à des participations (hors groupe)	267	FR
2675	Versements représentatifs d'apports non capitalisés (appel de fonds)	267	FR
2676	Avances consolidables	267	FR
2677	Autres créances rattachées à des participations	267	FR
2678	Intérêts courus	267	FR
268	Créances rattachées à des sociétés en participation	26	FR
2681	Principal	268	FR
2688	Intérêts courus	268	FR
269	Versements restant à effectuer sur titres de participation non libérés	26	FR
271	Titres immobilisés autres que les titres immobilisés de l'activité de portefeuille (droit de propriété)	2	FR
2711	Actions	271	FR
2718	Autres titres	271	FR
272	Titres immobilisés (droit de créance)	2	FR
2721	Obligations	272	FR
2722	Bons	272	FR
273	Titres immobilisés de l'activité de portefeuille	2	FR
274	Prêts	2	FR
2741	Prêts participatifs	274	FR
2742	Prêts aux associés	274	FR
2743	Prêts au personnel	274	FR
2748	Autres prêts	274	FR
275	Dépôts et cautionnements versés	2	FR
2751	Dépôts	275	FR
2755	Cautionnements	275	FR
276	Autres créances immobilisées	2	FR
2761	Créances diverses	276	FR
2768	Intérêts courus	276	FR
27682	Sur titres immobilisés (droit de créance)	2768	FR
27684	Sur prêts	2768	FR
27685	Sur dépôts et cautionnements	2768	FR
27688	Sur créances diverses	2768	FR
277	(Actions propres ou parts propres)	2	FR
2771	Actions propres ou parts propres	277	FR
2772	Actions propres ou parts propres en voie d’annulation	277	FR
279	Versements restant à effectuer sur titres immobilisés non libérés	2	FR
28	amortissements des immobilisations	2	FR
2801	Frais d'établissement (même ventilation que celle du compte 201)	28	FR
2803	Frais de recherche et de développement	28	FR
2805	Concessions et droits similaires, brevets, licences, logiciels, droits et valeurs similaires	28	FR
2807	Fonds commercial	28	FR
2808	Autres immobilisations incorporelles	28	FR
2811	Terrains de gisement	28	FR
2812	Agencements, aménagements de terrains (même ventilation que celle du compte 212)	28	FR
2813	Constructions (même ventilation que celle du compte 213)	28	FR
2814	Constructions sur sol d'autrui (même ventilation que celle du compte 214)	28	FR
2815	Installations, matériel et outillage industriels (même ventilation que celle du compte 215)	28	FR
2818	Autres immobilisations corporelles (même ventilation que celle du compte 218)	28	FR
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
2967	Créances rattachées à des participations (même ventilation que celle du compte 267)	296	FR
2968	Créances rattachées à des sociétés en participation (même ventilation que celle du compte 268)	296	FR
2971	Titres immobilisés autres que les titres immobilisés de l'activité de portefeuille -droit de propriété (même ventilation que celle du compte 271)	29	FR
2972	Titres immobilisés - droit de créance (même ventilation que celle du compte 272)	29	FR
2973	Titres immobilisés de l'activité de portefeuille	29	FR
2974	Prêts (même ventilation que celle du compte 274)	29	FR
2975	Dépôts et cautionnements versés (même ventilation que celle du compte 275)	29	FR
2976	Autres créances immobilisées (même ventilation que celle du compte 276)	29	FR
3	comptes de stocks et en cours	0	FR
311	Matières (ou groupe) A	3	FR
312	Matières (ou groupe) B	3	FR
317	Fournitures A, B, C, ...	3	FR
321	Matières consommables	3	FR
3211	Matières (ou groupe) C	321	FR
3212	Matières (ou groupe) D	321	FR
322	Fournitures consommables	3	FR
3221	Combustibles	322	FR
3222	Produits d'entretien	322	FR
3223	Fournitures d'atelier et d'usine	322	FR
3224	Fournitures de magasin	322	FR
3225	Fournitures de bureau	322	FR
326	Emballages	3	FR
3261	Emballages perdus	326	FR
3265	Emballages récupérables non identifiables	326	FR
3267	Emballages à usage mixte	326	FR
331	Produits en cours	3	FR
3311	Produits en cours P 1	331	FR
3312	Produits en cours P 2	331	FR
335	Travaux en cours	3	FR
3351	avaux en cours T 1	335	FR
3352	Travaux en cours T 2	335	FR
341	Etudes en cours	3	FR
3411	Etudes en cours E 1	341	FR
3412	Etudes en cours E 2	341	FR
345	Prestations de services en cours	3	FR
3451	Prestations de services S 1	345	FR
3452	Prestations de services S 2	345	FR
351	Produits intermédiaires	3	FR
3511	Produits intermédiaires (ou groupe) A	351	FR
3512	Produits intermédiaires (ou groupe) B	351	FR
355	Produits finis	3	FR
3551	Produits finis (ou groupe) A	355	FR
3552	Produits finis (ou groupe) B	355	FR
358	Produits résiduels (ou matières de récupération)	3	FR
3581	Déchets	358	FR
3585	Rebuts	358	FR
3586	Matières de récupération	358	FR
36	(compte à ouvrir, le cas échéant, sous l'intitulé " stocks provenant d'immobilisations ")	3	FR
371	Marchandises (ou groupe) A	3	FR
372	Marchandises (ou groupe) B	3	FR
38	(lorsque l'entité tient un inventaire permanent en comptabilité générale, le compte 38 peut être utilisé pour comptabiliser les stocks en voie d'acheminement, mis en dépôt ou donnés en consignation)	3	FR
39	provisions pour dépréciation des stocks et en-cours	3	FR
3911	Matières (ou groupe) A	39	FR
3912	Matières (ou groupe) B	39	FR
3917	Fournitures A, B, C, ...	39	FR
3921	Matières consommables (même ventilation que celle du compte 321)	39	FR
3922	Fournitures consommables (même ventilation que celle ducompte 322)	39	FR
3926	Emballages (même ventilation que celle du compte 326)	39	FR
3931	Produits en cours (même ventilation que celle du compte 331)	39	FR
3935	Travaux en cours (même ventilation que celle du compte 335)	39	FR
3941	Etudes en cours (même ventilation que celle du compte 341)	39	FR
3945	Prestations de services en cours (même ventilation que celle du compte 345)	39	FR
3951	Produits intermédiaires (même ventilation que celle du compte 351)	39	FR
3955	Produits finis (même ventilation que celle du compte 355)	39	FR
3971	Marchandise (ou groupe) A	39	FR
3972	Marchandise (ou groupe) B	39	FR
4	comptes de tiers	0	FR
40	fournisseurs et comptes rattaches	4	FR
401	Fournisseurs	40	FR
4011	Fournisseurs - Achats de biens et prestations de services	401	FR
4017	Fournisseurs - Retenues de garantie	401	FR
403	Fournisseurs - Effets à payer	40	FR
404	Fournisseurs d'immobilisations	40	FR
4041	Fournisseurs - Achats d'immobilisations	404	FR
4047	Fournisseurs d'immobilisations - Retenues de garantie	404	FR
405	Fournisseurs d'immobilisations - Effets à payer	40	FR
408	Fournisseurs - Factures non parvenues	40	FR
4081	Fournisseurs	408	FR
4084	Fournisseurs d'immobilisations	408	FR
4088	Fournisseurs - Intérêts courus	408	FR
4091	Fournisseurs - Avances et acomptes versés sur commandes	40	FR
4096	Fournisseurs - Créances pour emballages et matériel à rendre	40	FR
4097	Fournisseurs - Autres avoirs	40	FR
40971	Fournisseurs d'exploitation	4097	FR
40974	Fournisseurs d'immobilisations	4097	FR
4098	Rabais, remises, ristournes à obtenir et autres avoirs non encore reçus	40	FR
41	clients et comptes rattaches	4	FR
411	Clients	41	FR
4111	Clients - Ventes de biens ou de prestations de services	411	FR
4117	Clients - Retenues de garantie	411	FR
413	Clients - Effets à recevoir	41	FR
416	Clients douteux ou litigieux	41	FR
417	" Créances " sur travaux non encore facturables	41	FR
418	Clients - Produits non encore facturés	41	FR
4181	Clients - Factures à établir	418	FR
4188	Clients - Intérêts courus	418	FR
4191	Clients - Avances et acomptes reçus sur commandes	41	FR
4196	Clients - Dettes sur emballages et matériels consignés	41	FR
4197	Clients - Autres avoirs	41	FR
4198	Rabais, remises, ristournes à accorder et autres avoirs à établir	41	FR
42	Personnel et comptes rattaches	4	FR
422	Comités d'entreprises, d'établissement,...	42	FR
424	Participation des salariés aux résultats	42	FR
4246	Réserve spéciale (art. L. 442-2 du Code du travail)	424	FR
4248	Comptes courants	424	FR
425	Personnel - Avances et acomptes	42	FR
426	Personnel - Dépôts	42	FR
427	Personnel - Oppositions	42	FR
4282	Dettes provisionnées pour congés à payer	42	FR
4284	Dettes provisionnées pour participation des salariés aux résultats	42	FR
4286	Autres charges à payer	42	FR
4287	Produits à recevoir	42	FR
431	Sécurité sociale	4	FR
437	Autres organismes sociaux	4	FR
438	Organismes sociaux - Charges à payer et produits à recevoir	4	FR
4382	Charges sociales sur congés à payer	438	FR
4386	Autres charges à payer	438	FR
4387	Produits à recevoir	438	FR
44	État et autres collectivités publiques	4	FR
441	État - Subventions à recevoir	44	FR
4411	Subventions d'investissement	441	FR
4417	Subventions d'exploitation	441	FR
4418	Subventions d'équilibre	441	FR
4419	Avances sur subventions	441	FR
442	Etat - Impôts et taxes recouvrables sur des tiers	44	FR
4424	Obligataires	442	FR
4425	Associés	442	FR
443	Opérations particulières avec l'Etat les collectivités publiques, les organismes internationaux	44	FR
4431	Créances sur l'Etat résultant de la suppression de la règle du décalage d'un mois en matière de T.V.A.	443	FR
4438	Intérêts courus sur créances figurant au 4431	443	FR
4452	T.V.A. due intracommunautaire	44	FR
4455	Taxes sur le chiffre d'affaires à décaisser	44	FR
44551	T.V.A. à décaisser	4455	FR
44558	Taxes assimilées à la T.V.A.	4455	FR
4456	Taxes sur le chiffre d'affaires déductibles	44	FR
44562	T.V.A. sur immobilisations	4456	FR
44563	T.V.A. transférée par d'autres entreprises	4456	FR
44566	T.V.A. sur autres biens et services	4456	FR
44567	Crédit de T.V.A. à reporter	4456	FR
44568	Taxes assimilées à la T.V.A.	4456	FR
4457	Taxes sur le chiffre d'affaires collectées par l'entreprise	44	FR
44571	T.V.A. collectée	4457	FR
44578	Taxes assimilées à la T.V.A.	4457	FR
4458	Taxes sur le chiffre d'affaires à régulariser ou en attente	44	FR
44581	Acomptes - Régime simplifié d'imposition	4458	FR
44582	Acomptes - Régime de forfait	4458	FR
44583	Remboursement de taxes sur le chiffre d'affaires demandé	4458	FR
44584	T.V.A. récupérée d'avance	4458	FR
44586	Taxes sur le chiffre d'affaires sur factures non parvenues	4458	FR
44587	Taxes sur le chiffres d'affaires sur factures à établir	4458	FR
446	Obligations cautionnées	44	FR
448	Etat - Charges à payer et produits à recevoir	44	FR
4482	Charges fiscales sur congés à payer	448	FR
4486	Charges à payer	448	FR
4487	Produits à recevoir	448	FR
451	Groupe	4	FR
4551	Principal	4	FR
4558	Intérêts courus	4	FR
456	Associés - Opérations sur le capital	4	FR
4561	Associés - Comptes d'apport en société	456	FR
45611	Apports en nature	4561	FR
45615	Apports en numéraire	4561	FR
4562	Apporteurs - Capital appelé, non versé	456	FR
45621	Actionnaires - Capital souscrit et appelé, non versé	4562	FR
45625	Associés - Capital appelé, non versé	4562	FR
4563	Associés - Versements reçus sur augmentation de capital	456	FR
4564	Associés - Versements anticipés	456	FR
4566	Actionnaires défaillants	456	FR
4567	Associés - Capital à rembourser	456	FR
457	Associés - Dividendes à payer	4	FR
458	Associés - Opérations faites en commun et en G.I.E.	4	FR
4581	Opérations courantes	458	FR
4588	Intérêts courus	458	FR
462	Créances sur cessions d'immobilisations	4	FR
464	Dettes sur acquisitions de valeurs mobilières de placement	4	FR
465	Créances sur cessions de valeurs mobilières de placement	4	FR
467	Autres comptes débiteurs ou créditeurs	4	FR
468	Divers - Charges à payer et produits à recevoir	4	FR
4686	Charges à payer	468	FR
4687	Produits à recevoir	468	FR
471	Comptes d'attente	4	FR
472	Comptes d'attente	4	FR
473	Comptes d'attente	4	FR
474	Comptes d'attente	4	FR
475	Comptes d'attente	4	FR
476	Différence de conversion - Actif	4	FR
4761	Diminution des créances	476	FR
4762	Augmentation des dettes	476	FR
4768	Différences compensées par couverture de change	476	FR
477	Différences de conversion - Passif	4	FR
4771	Augmentation des créances	477	FR
4772	Diminution des dettes	477	FR
4778	Différences compensées par couverture de change	477	FR
478	Autres comptes transitoires	4	FR
48	comptes de régularisation	4	FR
4811	Charges différées	48	FR
4812	Frais d'acquisition des immobilisations	481	FR
4816	Frais d'émission des emprunts	481	FR
4818	Charges à étaler	481	FR
488	Comptes de répartition périodique des charges et des produits	48	FR
4886	Charges	48	FR
4887	Produits	48	FR
49	provisions pour dépréciation des comptes de tiers	4	FR
495	Provisions pour dépréciation des comptes du groupe et des associés	49	FR
4951	Comptes du groupe	495	FR
4955	Comptes courants des associés	495	FR
4958	Opérations faites en commun et en G.I.E.	495	FR
4962	Créances sur cessions d'immobilisations	49	FR
4965	Créances sur cessions de valeurs mobilières de placement	49	FR
4967	Autres comptes débiteurs	49	FR
5	comptes financiers	0	FR
501	Parts dans des entreprises liées	5	FR
502	Actions propres	5	FR
503	Actions	5	FR
5031	Titres cotés	503	FR
5035	Titres non cotés	503	FR
504	Autres titres conférant un droit de propriété	5	FR
505	Obligations et bons émis par la société et rachetés par elle	5	FR
506	Obligations	5	FR
5061	Titres cotés	506	FR
5065	Titres non cotés	506	FR
507	Bons du Trésor et bons de caisse à court terme	5	FR
508	Autres valeurs mobilières de placement et autres créances assimilées	5	FR
5081	Autres valeurs mobilières	508	FR
5082	Bons de souscription	508	FR
5088	Intérêts courus sur obligations, bons et valeurs assimilés	508	FR
509	Versements restant à effectuer sur valeurs mobilières de placement non libérées	5	FR
511	Valeurs à l'encaissement	5	FR
5111	Coupons échus à l'encaissement	511	FR
5112	Chèques à encaisser	511	FR
5113	Effets à l'encaissement	511	FR
5114	Effets à l'escompte	511	FR
512	Banques	5	FR
5121	Comptes en monnaie nationale	512	FR
5124	Comptes en devises	512	FR
514	Chèques postaux	5	FR
515	" Caisses " du Trésor et des établissements publics	5	FR
516	Sociétés de bourse	5	FR
517	Autres organismes financiers	5	FR
518	Intérêts courus	5	FR
5181	Intérêts courus à payer	518	FR
5188	Intérêts courus à recevoir	518	FR
519	Concours bancaires courants	5	FR
5191	Crédit de mobilisation de créances commerciales (CMCC)	519	FR
5193	Mobilisation de créances nées à l'étranger	519	FR
5198	Intérêts courus sur concours bancaires courants	519	FR
52	Instruments de trésorerie	5	FR
531	Caisse siège social	5	FR
5311	Caisse en monnaie nationale	531	FR
5314	Caisse en devises	531	FR
532	Caisse succursale (ou usine) A	5	FR
533	Caisse succursale (ou usine) B	5	FR
59	provisions pour dépréciation des comptes financiers	5	FR
5903	Actions	59	FR
5904	Autres titres conférant un droit de propriété	59	FR
5906	Obligations	59	FR
5908	Autres valeurs mobilières de placement et créances assimilées	59	FR
6	comptes de charges	0	FR
601	Achats stockés - Matières premières (et fournitures)	6	FR
6011	Matières (ou groupe) A	601	FR
6012	Matières (ou groupe) B	601	FR
6017	Fournitures A, B, C, ...	601	FR
602	Achats stockés - Autres approvisionnements	6	FR
6021	Matières consommables	602	FR
60211	Matières (ou groupe) C	6021	FR
60212	Matières (ou groupe) D	6021	FR
6022	Fournitures consommables	602	FR
60221	Combustibles	6022	FR
60222	Produits d'entretien	6022	FR
60223	Fournitures d'atelier et d'usine	6022	FR
60224	Fournitures de magasin	6022	FR
60225	Fourniture de bureau	6022	FR
6026	Emballages	602	FR
60261	Emballages perdus	6026	FR
60265	ballages récupérables non identifiables	6026	FR
60267	Emballages à usage mixte	6026	FR
604	Achats d'études et prestations de services	6	FR
605	Achats de matériel, équipements et travaux	6	FR
606	Achats non stockés de matière et fournitures	6	FR
6061	Fournitures non stockables (eau, énergie, ...)	606	FR
6063	Fournitures d'entretien et de petit équipement	606	FR
6064	Fournitures administratives	606	FR
6068	Autres matières et fournitures	606	FR
607	Achats de marchandises	6	FR
6071	Marchandise (ou groupe) A	607	FR
6072	Marchandise (ou groupe) B	607	FR
608	(Compte réservé, le cas échéant, à la récapitulation des frais accessoires incorporés aux achats)	6	FR
609	Rabais, remises et ristournes obtenus sur achats	6	FR
6091	de matières premières (et fournitures)	609	FR
6092	d'autres approvisionnements stockés	609	FR
6094	d'études et prestations de services	609	FR
6095	de matériel, équipements et travaux	609	FR
6096	d'approvisionnements non stockés	609	FR
6097	de marchandises	609	FR
6098	Rabais, remises et ristournes non affectés	609	FR
6031	Variation des stocks de matières premières (et fournitures)	6	FR
6032	Variation des stocks des autres approvisionnements	6	FR
6037	Variation des stocks de marchandises	6	FR
61	autres charges externes - Services extérieurs	6	FR
611	Sous-traitance générale	61	FR
612	Redevances de crédit-bail	61	FR
6122	Crédit-bail mobilier	612	FR
6125	Crédit-bail immobilier	612	FR
613	Locations	61	FR
6132	Locations immobilières	613	FR
6135	Locations mobilières	613	FR
6136	Malis sur emballages	613	FR
614	Charges locatives et de copropriété	61	FR
615	Entretien et réparations	61	FR
6152	sur biens immobiliers	615	FR
6155	sur biens mobiliers	615	FR
6156	Maintenance	615	FR
616	Primes d'assurances	61	FR
6161	Multirisques	616	FR
6162	Assurance obligatoire dommage construction	616	FR
6163	Assurance-transport	616	FR
61636	sur achats	6163	FR
61637	sur ventes	6163	FR
61638	sur autres biens	6163	FR
6164	Risques d'exploitation	616	FR
6165	Insolvabilité clients	616	FR
617	Etudes et recherches	61	FR
618	Divers	61	FR
6181	Documentation générale	618	FR
6183	Documentation technique	618	FR
6185	Frais de colloques, séminaires, conférences	618	FR
619	Rabais, remises et ristournes obtenus sur services extérieurs	61	FR
62	autres charges externes - Autres services extérieurs	6	FR
621	Personnel extérieur à l'entreprise	62	FR
6211	Personnel intérimaire	621	FR
6214	Personnel détaché ou prêté à l'entreprise	621	FR
622	Rémunérations d'intermédiaires et honoraires	62	FR
6221	Commissions et courtages sur achats	622	FR
6222	Commissions et courtages sur ventes	622	FR
6224	Rémunérations des transitaires	622	FR
6225	Rémunérations d'affacturage	622	FR
6226	Honoraires	622	FR
6227	Frais d'actes et de contentieux	622	FR
6228	Divers	622	FR
623	Publicité, publications, relations publiques	62	FR
6231	Annonces et insertions	623	FR
6232	Echantillons	623	FR
6233	Foires et expositions	623	FR
6234	Cadeaux à la clientèle	623	FR
6235	Primes	623	FR
6236	Catalogues et imprimés	623	FR
6237	Publications	623	FR
6238	Divers (pourboires, dont courant, ...)	623	FR
624	Transports de biens et transports collectifs du personnel	62	FR
6241	Transports sur achats	624	FR
6242	Transports sur ventes	624	FR
6243	Transports entre établissements ou chantiers	624	FR
6244	Transports administratifs	624	FR
6247	Transports collectifs du personnel	624	FR
6248	Divers	624	FR
625	Déplacements, missions et réceptions	62	FR
6251	Voyages et déplacements	625	FR
6255	Frais de déménagement	625	FR
6256	Missions	625	FR
6257	Réceptions	625	FR
626	Frais postaux et de télécommunications	62	FR
627	Services bancaires et assimilés	62	FR
6271	Frais sur titres (achat, vente, garde)	627	FR
6272	Commissions et frais sur émission d'emprunts	627	FR
6275	Frais sur effets	627	FR
6276	Location de coffres	627	FR
6278	Autres frais et commissions sur prestations de services	627	FR
628	Divers	62	FR
6281	Concours divers (cotisations, ...)	628	FR
6284	Frais de recrutement de personnel	628	FR
629	Rabais, remises et ristournes obtenus sur autres services extérieurs	62	FR
631	Impôts, taxes et versements assimilés sur rémunérations (administrations des impôts)	6	FR
6311	Taxe sur les salaires	631	FR
6312	Taxe d'apprentissage	631	FR
6313	Participation des employeurs à la formation professionnelle continue	631	FR
6314	Cotisation pour défaut d'investissement obligatoire dans la construction	631	FR
6318	Autres	631	FR
633	Impôts, taxes et versements assimilés sur rémunérations (autres organismes)	6	FR
6331	Versement de transport	633	FR
6332	Allocations logement	633	FR
6333	Participation des employeurs à la formation professionnelle continue	633	FR
6334	Participation des employeurs à l'effort de construction	633	FR
6335	Versements libératoires ouvrant droit à l'exonération de la taxe d'apprentissage	633	FR
6338	Autres	633	FR
635	Autres impôts, taxes et versements assimilés (administrations des impôts)	6	FR
6351	Impôts directs (sauf impôts sur les bénéfices)	635	FR
63511	Taxe professionnelle	6351	FR
63512	Taxes foncières	6351	FR
63513	Autres impôts locaux	6351	FR
63514	Taxe sur les véhicules des sociétés	6351	FR
6352	Taxe sur le chiffre d'affaires non récupérables	635	FR
6353	Impôts indirects	635	FR
6354	Droits d'enregistrement et de timbre	635	FR
63541	Droits de mutation	6354	FR
6358	Autres droits	635	FR
637	Autres impôts, taxes et versements assimilés (autres organismes)	6	FR
6371	Contribution sociale de solidarité à la charge des sociétés	637	FR
6372	Taxes perçues par les organismes publics internationaux	637	FR
6374	Impôts et taxes exigibles à l'Etranger	637	FR
6378	Taxes diverses	637	FR
64	Charges de personnel	6	FR
6411	Salaires, appointements	64	FR
6412	Congés payés	641	FR
6413	Primes et gratifications	641	FR
6414	Indemnités et avantages divers	641	FR
6415	Supplément familial	641	FR
6451	Cotisations à l'URSSAF	64	FR
6452	Cotisations aux mutuelles	64	FR
6453	Cotisations aux caisses de retraites	64	FR
6454	Cotisations aux ASSEDIC	64	FR
6458	Cotisations aux autres organismes sociaux	64	FR
647	Autres charges sociales	64	FR
6471	Prestations directes	647	FR
6472	Versements aux comités d'entreprise et d'établissement	647	FR
6473	Versements aux comités d'hygiène et de sécurité	647	FR
6474	Versements aux autres œuvres sociales	647	FR
6475	Médecine du travail, pharmacie	647	FR
648	Autres charges de personnel	64	FR
651	Redevances pour concessions, brevets, licences, marques, procédés, logiciels, droits et valeurs similaires	6	FR
6511	Redevances pour concessions, brevets, licences, marques, procédés, logiciels	651	FR
6516	Droits d'auteur et de reproduction	651	FR
6518	Autres droits et valeurs similaires	651	FR
653	Jetons de présence	6	FR
654	Pertes sur créances irrécouvrables	6	FR
6541	Créances de l'exercice	654	FR
6544	Créances des exercices antérieurs	654	FR
655	Quotes-parts de résultat sur opérations faites en commun	6	FR
6551	Quote-part de bénéfice transférée (comptabilité du gérant)	655	FR
6555	Quote-part de perte supportée (comptabilité des associés non gérants)	655	FR
658	Charges diverses de gestion courante	6	FR
661	Charges d'intérêts	6	FR
6611	Intérêts des emprunts et dettes	661	FR
66116	des emprunts et dettes assimilées	6611	FR
66117	des dettes rattachées à des participations	6611	FR
6615	Intérêts des comptes courants et des dépôts créditeurs	661	FR
6616	Intérêts bancaires et sur opérations de financement (escompte,...)	661	FR
6617	Intérêts des obligations cautionnées	661	FR
6618	Intérêts des autres dettes	661	FR
66181	des dettes commerciales	6618	FR
66188	des dettes diverses	66188	FR
664	Pertes sur créances liées à des participations	6	FR
665	Escomptes accordés	6	FR
666	Pertes de change	6	FR
667	Charges nettes sur cessions de valeurs mobilières de placement	6	FR
668	Autres charges financières	6	FR
671	Charges exceptionnelles sur opérations de gestion	6	FR
6711	Pénalités sur marchés (et dédits payés sur achats et ventes)	671	FR
6712	Pénalités, amendes fiscales et pénales	671	FR
6713	Dons, libéralités	671	FR
6714	Créances devenues irrécouvrables dans l'exercice	671	FR
6715	Subventions accordées	671	FR
6717	Rappel d'impôts (autres qu'impôts sur les bénéfices)	671	FR
6718	Autres charges exceptionnelles sur opérations de gestion	671	FR
672	(Compte à la disposition des entités pour enregistrer, en cours d'exercice, les charges sur exercices antérieurs)	6	FR
675	Valeurs comptables des éléments d'actif cédés	6	FR
6751	Immobilisations incorporelles	675	FR
6752	Immobilisations corporelles	675	FR
6756	Immobilisations financières	675	FR
6758	Autres éléments d'actif	675	FR
678	Autres charges exceptionnelles	6	FR
6781	Malis provenant de clauses d'indexation	678	FR
6782	Lots	678	FR
6783	Malis provenant du rachat par l'entreprise d'actions et obligations émises par elle-même	678	FR
6788	Charges exceptionnelles diverses	678	FR
68	Dotations aux amortissements et aux provisions	6	FR
6811	Dotations aux amortissements sur immobilisations incorporelles et corporelles	68	FR
68111	Immobilisations incorporelles	6811	FR
68112	Immobilisations corporelles	6811	FR
6812	Dotations aux amortissements des charges d'exploitation à répartir	68	FR
6815	Dotations aux provisions pour risques et charges d'exploitation	68	FR
6816	Dotations aux provisions pour dépréciation des immobilisations incorporelles et corporelles	68	FR
68161	Immobilisations incorporelles	6816	FR
68162	Immobilisations corporelles	6816	FR
6817	Dotations aux provisions pour dépréciation des actifs circulants	68	FR
68173	Stocks et en-cours	6817	FR
68174	Créances	6817	FR
6861	Dotations aux amortissements des primes de remboursement des obligations	68	FR
6865	Dotations aux provisions pour risques et charges financiers	68	FR
6866	Dotations aux provisions pour dépréciation des éléments financiers	68	FR
68662	Immobilisations financières	6866	FR
68665	Valeurs mobilières de placement	6866	FR
6868	Autres dotations	68	FR
6871	Dotations aux amortissements exceptionnels des immobilisations	68	FR
6872	Dotations aux provisions réglementées (immobilisations)	68	FR
68725	Amortissements dérogatoires	6872	FR
6873	Dotations aux provisions réglementées (stocks)	68	FR
6874	Dotations aux autres provisions réglementées	68	FR
6875	Dotations aux provisions pour risques et charges exceptionnels	68	FR
6876	Dotations aux provisions pour dépréciations exceptionnelles	68	FR
69	participation des salaries - impôts sur les benefices et assimiles	6	FR
6951	Impôts dus en France	69	FR
6952	Contribution additionnelle à l'impôt sur les bénéfices	69	FR
6954	Impôts dus à l'étranger	69	FR
696	Suppléments d'impôt sur les sociétés liés aux distributions	69	FR
698	Intégration fiscale	69	FR
6981	Intégration fiscale - Charges	698	FR
6989	Intégration fiscale - Produits	698	FR
7	comptes de produits	0	FR
70	ventes de produits fabriques, prestations de services, marchandises	7	FR
7011	Produits finis (ou groupe) A	70	FR
7012	Produits finis (ou groupe) B	70	FR
702	Ventes de produits intermédiaires	70	FR
703	Ventes de produits résiduels	70	FR
704	Travaux	70	FR
7041	Travaux de catégorie (ou activité) A	704	FR
7042	Travaux de catégorie (ou activité) B	704	FR
705	Etudes	7	FR
7071	Marchandises (ou groupe) A	70	FR
7072	Marchandises (ou groupe) B	70	FR
7081	Produits des services exploités dans l'intérêt du personnel	70	FR
7082	Commissions et courtages	70	FR
7083	Locations diverses	70	FR
7084	Mise à disposition de personnel facturée	70	FR
7085	Ports et frais accessoires facturés	70	FR
7086	Bonis sur reprises d'emballages consignés	70	FR
7087	Bonifications obtenues des clients et primes sur ventes	70	FR
7088	Autres produits d'activités annexes (cessions d'approvisionnements,...)	70	FR
7091	sur ventes de produits finis	70	FR
7092	sur ventes de produits intermédiaires	70	FR
7094	sur travaux	70	FR
7095	sur études	70	FR
7096	sur prestations de services	70	FR
7097	sur ventes de marchandises	70	FR
7098	sur produits des activités annexes	70	FR
71	production stockée (ou déstockage)	7	FR
7133	Variation des en-cours de production de biens	71	FR
71331	Produits en cours	7133	FR
71335	Travaux en cours	7133	FR
7134	Variation des en-cours de production de services	71	FR
71341	Etudes en cours	7134	FR
71345	Prestations de services en cours	7134	FR
7135	Variation des stocks de produits	71	FR
71351	Produits intermédiaires	7135	FR
71355	Produits finis	7135	FR
71358	Produits résiduels	7135	FR
721	Immobilisations incorporelles	7	FR
722	Immobilisations corporelles	7	FR
731	Produits nets partiels sur opérations en cours (à subdiviser par opération)	7	FR
739	Produits nets partiels sur opérations terminées	7	FR
751	Redevances pour concessions, brevets, licences, marques, procédés, logiciels, droits et valeurs similaires	7	FR
7511	Redevances pour concessions, brevets, licences, marques, procédés, logiciels	751	FR
7516	Droits d'auteur et de reproduction	751	FR
7518	Autres droits et valeurs similaires	751	FR
752	Revenus des immeubles non affectés à des activités professionnelles	7	FR
7551	Quote-part de perte transférée (comptabilité du gérant)	7	FR
7555	Quote-part de bénéfice attribuée (comptabilité des associés non-gérants)	7	FR
758	Produits divers de gestion courante	7	FR
761	Produits de participations	7	FR
7611	Revenus des titres de participation	761	FR
7616	Revenus sur autres formes de participation	761	FR
7617	Revenus des créances rattachées à des participations	761	FR
762	Produits des autres immobilisations financières	7	FR
7621	Revenus des titres immobilisés	762	FR
7626	Revenus des prêts	762	FR
7627	Revenus des créances immobilisées	762	FR
763	Revenus des autres créances	7	FR
7631	Revenus des créances commerciales	763	FR
7638	Revenus des créances diverses	763	FR
764	Revenus des valeurs mobilières de placement	7	FR
765	Escomptes obtenus	7	FR
766	Gains de change	7	FR
767	Produits nets sur cessions de valeurs mobilières de placement	7	FR
768	Autres produits financiers	7	FR
771	Produits exceptionnels sur opérations de gestion	7	FR
7711	Dédits et pénalités perçus sur achats et sur ventes	771	FR
7713	Libéralités reçues	771	FR
7714	Rentrées sur créances amorties	771	FR
7715	Subventions d'équilibre	771	FR
7717	Dégrèvements d'impôts autres qu'impôts sur les bénéfices	771	FR
7718	Autres produits exceptionnels sur opérations de gestion	771	FR
772	(Compte à la disposition des entités pour enregistrer, en cours d'exercice, les produits sur exercices antérieurs)	7	FR
775	Produits des cessions d'éléments d'actif	7	FR
7751	Immobilisations incorporelles	775	FR
7752	Immobilisations corporelles	775	FR
7756	Immobilisations financières	775	FR
7758	Autres éléments d'actif	775	FR
777	Quote-part des subventions d'investissement virée au résultat de l'exercice	7	FR
778	Autres produits exceptionnels	7	FR
7781	Bonis provenant de clauses d'indexation	778	FR
7782	Lots	778	FR
7783	Bonis provenant du rachat par l'entreprise d'actions et d'obligations émises par elle-même	778	FR
7788	Produits exceptionnels divers	778	FR
78	Reprises sur amortissements et provisions	7	FR
7811	Reprises sur amortissements des immobilisations incorporelles et corporelles	78	FR
78111	Immobilisations incorporelles	7811	FR
78112	Immobilisations corporelles	7811	FR
7815	Reprises sur provisions pour risques et charges d'exploitation	78	FR
7816	Reprises sur provisions pour dépréciation des immobilisations incorporelles et corporelles	78	FR
78161	Immobilisations incorporelles	7816	FR
78162	Immobilisations corporelles	7816	FR
7817	Reprises sur provisions pour dépréciation des actifs circulants	78	FR
78173	Stocks et en-cours	7817	FR
78174	Créances	7817	FR
7865	Reprises sur provisions pour risques et charges financiers	78	FR
7866	Reprises sur provisions pour dépréciation des éléments financiers	78	FR
78662	Immobilisations financières	7866	FR
78665	Valeurs mobilières de placements	7866	FR
7872	Reprises sur provisions réglementées (immobilisations)	78	FR
78725	Amortissements dérogatoires	7872	FR
78726	Provision spéciale de réévaluation	7872	FR
78727	Plus-values réinvesties	7872	FR
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
