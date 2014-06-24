<?php

q("TRUNCATE TABLE vkGroups");
q("TRUNCATE TABLE vkGroupsSearch");
q("UPDATE vkKeywords SET passed=0, userId=0");
