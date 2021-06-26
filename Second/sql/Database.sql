CREATE TABLE `Azati`.`people` (
                                  `id` INT NOT NULL AUTO_INCREMENT,
                                  `name` VARCHAR(45) NOT NULL,
                                  `family` VARCHAR(45) NOT NULL,
                                  `patronymic` VARCHAR(45) NOT NULL,
                                  `path` VARCHAR(45) NOT NULL,
                                  `id_speciality` INT NOT NULL ,
                                  PRIMARY KEY (`id`),
                                  CONSTRAINT `people_spetialty`
                                      FOREIGN KEY (`id_speciality`)
                                      REFERENCES `Azati`.`specialities` (`id`)
                                      ON DELETE CASCADE
                                      ON UPDATE CASCADE
                              );


CREATE TABLE `Azati`.`specialities` (
                                     `id` INT NOT NULL AUTO_INCREMENT,
                                     `speciality` VARCHAR(45) NOT NULL,
                                     PRIMARY KEY (`id`));

CREATE TABLE `Azati`.`skill` (
                                 `id` INT NOT NULL AUTO_INCREMENT,
                                 `skill` VARCHAR(200) NOT NULL,
                                  UNIQUE INDEX `SKILLSKILL` (`skill` ASC),
                                 PRIMARY KEY (`id`));

CREATE TABLE `Azati`.`people_skill` (
                                        `id_people` INT NOT NULL,
                                        `id_skill` INT NOT NULL,
                                        CONSTRAINT `people_skill_id_people`
                                            FOREIGN KEY (`id_people`)
                                                REFERENCES `Azati`.`people` (`id`)
                                                ON DELETE CASCADE
                                                ON UPDATE CASCADE,
                                        CONSTRAINT `people_skill_id_skill`
                                            FOREIGN KEY (`id_skill`)
                                                REFERENCES `Azati`.`skill` (`id`)
                                                ON DELETE CASCADE
                                                ON UPDATE CASCADE);

INSERT INTO `Azati`.`specialities` (`speciality`) VALUES ('IT'), ('Marketing'), ('Sales');