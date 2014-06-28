<?

db()->query("UPDATE vkGroups SET dateExport=0 WHERE dateExport!=0");
db()->query("DELETE FROM vkTopics");
db()->query("DELETE FROM vkTopicComments");