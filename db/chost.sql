/*==============================================================*/
/* Nom de SGBD :  MySQL 5.0                                     */
/* Date de création :  22/03/2018 16:17:49                      */
/*==============================================================*/


drop table if exists ACHATS;

drop table if exists CLIENTS;

drop table if exists CODES_ACTIVATION;

drop table if exists FACTURES;

drop table if exists MESSAGES;

drop table if exists OFFRES;

drop table if exists SOUSCRIPTION;

drop table if exists TICKETS;

/*==============================================================*/
/* Table : ACHATS                                               */
/*==============================================================*/
create table ACHATS
(
   ID_OFFRE             int(11) not null,
   ID_FACTURE           int(11) not null,
   primary key (ID_OFFRE, ID_FACTURE)
);

/*==============================================================*/
/* Table : CLIENTS                                              */
/*==============================================================*/
create table CLIENTS
(
   ID_CLIENT            int(11) not null auto_increment,
   PRENOM               varchar(255),
   NOM                  varchar(255),
   EMAIL                varchar(255),
   PASSWORD             varchar(255),
   ADRESSE              text,
   CODEPOSTAL           varchar(10),
   VILLE                varchar(255),
   TELEPHONE            varchar(50),
   CREDIT               decimal(10,2),
   COMPTE_ACTIF         int,
   TOKEN_ALEATOIRE      varchar(128),
   TYPE_COMPTE          varchar(64),
   primary key (ID_CLIENT)
);

/*==============================================================*/
/* Table : CODES_ACTIVATION                                     */
/*==============================================================*/
create table CODES_ACTIVATION
(
   ID_CODE              int(11) not null auto_increment,
   CODE                 varchar(128),
   VALEUR_CODE          decimal(10,2),
   primary key (ID_CODE)
);

/*==============================================================*/
/* Table : FACTURES                                             */
/*==============================================================*/
create table FACTURES
(
   ID_FACTURE           int(11) not null auto_increment,
   ID_CLIENT            int(11) not null,
   DATE_FACTURE         datetime,
   TOTAL_FACTURE        decimal(10,2),
   TVA                  int,
   primary key (ID_FACTURE)
);

/*==============================================================*/
/* Table : MESSAGES                                             */
/*==============================================================*/
create table MESSAGES
(
   MESSAGE_TICKET       text,
   DATE_MESSAGE         datetime,
   PRENOM_AUTEUR        varchar(255),
   ID_MESSAGE           int(11) not null auto_increment,
   ID_TICKET            int(10) not null,
   primary key (ID_MESSAGE)
);

/*==============================================================*/
/* Table : OFFRES                                               */
/*==============================================================*/
create table OFFRES
(
   ID_OFFRE             int(11) not null auto_increment,
   NOM_OFFRE            varchar(50),
   PRIX_OFFRE           decimal(10,2),
   ESPACE_STOCKAGE      int,
   primary key (ID_OFFRE)
);

/*==============================================================*/
/* Table : SOUSCRIPTION                                         */
/*==============================================================*/
create table SOUSCRIPTION
(
   ID_CLIENT            int(11) not null,
   ID_OFFRE             int(11) not null,
   EXPIRE               datetime,
   IDENTIFIANT_SOUSCRIPTION varchar(50),
   PASSWORD_SOUSCRIPTION varchar(100),
   SOUSDOMAINE          varchar(100),
   primary key (ID_CLIENT, ID_OFFRE)
);

/*==============================================================*/
/* Table : TICKETS                                              */
/*==============================================================*/
create table TICKETS
(
   TYPE_PROBLEME        varchar(255),
   ID_TICKET            int(10) not null auto_increment,
   ID_CLIENT            int(11) not null,
   LOCK_TICKET          int,
   primary key (ID_TICKET)
);

alter table ACHATS add constraint FK_ACHATS foreign key (ID_OFFRE)
      references OFFRES (ID_OFFRE) on delete restrict on update restrict;

alter table ACHATS add constraint FK_ACHATS2 foreign key (ID_FACTURE)
      references FACTURES (ID_FACTURE) on delete restrict on update restrict;

alter table FACTURES add constraint FK_COMMANDE foreign key (ID_CLIENT)
      references CLIENTS (ID_CLIENT) on delete restrict on update restrict;

alter table MESSAGES add constraint FK_CONTIENT_MESSAGE foreign key (ID_TICKET)
      references TICKETS (ID_TICKET) on delete restrict on update restrict;

alter table SOUSCRIPTION add constraint FK_SOUSCRIPTION foreign key (ID_CLIENT)
      references CLIENTS (ID_CLIENT) on delete restrict on update restrict;

alter table SOUSCRIPTION add constraint FK_SOUSCRIPTION2 foreign key (ID_OFFRE)
      references OFFRES (ID_OFFRE) on delete restrict on update restrict;

alter table TICKETS add constraint FK_OUVRE_TICKET foreign key (ID_CLIENT)
      references CLIENTS (ID_CLIENT) on delete restrict on update restrict;

