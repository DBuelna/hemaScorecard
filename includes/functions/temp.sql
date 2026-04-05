SELECT
    se.eventID,
    se.eventYear,
    se.eventName,
    se.eventCity,
    se.eventProvince,
    sc.countryName,
    se.eventStartDate,
    se.eventEndDate,
    IF (isArchived = 1, 'complete',
        IF(publishMatches = 1, 'active',
            IF(publishDescription = 1
                OR publishRoster = 1
                OR publishSchedule = 1
                OR publishRules = 1, 'upcoming','hidden'))) AS eventStatus
FROM systemEvents se
INNER JOIN systemCountries sc USING(countryIso2)
LEFT JOIN eventPublication USING(eventID)
WHERE 
    isArchived = 1 OR 
    publishDescription = 1 OR 
    publishRoster = 1 OR 
    publishSchedule = 1 OR 
    publishMatches = 1 OR 
    publishRules = 1
ORDER BY eventStartDate ASC



SELECT
    se.eventID,
    se.eventYear,
    se.eventName,
    se.eventCity,
    se.eventProvince,
    sc.countryName,
    se.eventStartDate,
    se.eventEndDate,
    CASE
        WHEN se.isArchived = 1 THEN 'complete'
        WHEN ep.publishMatches = 1 THEN 'active'
        WHEN COALESCE(ep.publishDescription, 0) = 1
          OR COALESCE(ep.publishRoster, 0) = 1
          OR COALESCE(ep.publishSchedule, 0) = 1
          OR COALESCE(ep.publishRules, 0) = 1
        THEN 'upcoming'
        ELSE 'hidden'
    END AS eventStatus
FROM systemEvents AS se
INNER JOIN systemCountries AS sc
    USING (countryIso2)
LEFT JOIN eventPublication AS ep
    USING (eventID)
WHERE
    se.isArchived = 1
    OR ep.publishDescription = 1
    OR ep.publishRoster = 1
    OR ep.publishSchedule = 1
    OR ep.publishMatches = 1
    OR ep.publishRules = 1
ORDER BY se.eventStartDate ASC;

/*
`SHOW COLUMNS FROM ScorecardV5.systemEvents;`
eventID
eventName
eventAbbreviation
eventYear
eventStartDate
eventEndDate
regionCode
countryIso2
eventProvince
eventCity
eventStatus
isArchived
limitStaffConflicts
isMetaEvent

`SHOW COLUMNS FROM ScorecardV5.systemCountries;`
countryIso2
countryTitle
countryName
countryIso3
countryNumCode

`SHOW COLUMNS FROM ScorecardV5.eventPublication;`
publicationID
eventID
publishDescription
publishRoster
publishSchedule
publishMatches
publishRules
*/
