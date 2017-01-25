SELECT CONCAT(lf.name, " TO ", lt.name)
FROM udo_tripleg AS tl
LEFT JOIN udo_location AS pf ON (pf.id = tl.locationFromPoint_id)
LEFT JOIN udo_location AS pt ON (pt.id = tl.locationToPoint_id)
LEFT JOIN udo_route AS ro ON (ro.locationFrom_id = pf.parent_id AND ro.locationTo_id = pt.parent_id)
LEFT JOIN udo_location AS lf ON (lf.id = ro.locationFrom_id)
LEFT JOIN udo_location AS lt ON (lt.id = ro.locationTo_id)
WHERE tl.truck_id IN (303,349,360,362,364,365,366,367,368,369,370,371,374,375,376,377,379,380,381,385,386,387,388,389,390,391,397,398,399,401,402,403,404,405,419,420,421,422,423,424,431,432,433,434,435,436,437,438,439,440,441,442,443,444,445,446,462,466,467,468,469,471,478,479,480,481,482,483,484,485,486,487,494,495,496,497,498,499,500,501,502,503,504,508,509,510,511,512,513,514,515,516,517,518,519,520,524,525,526,527,528,529,530,531,532,533,534,535,536,537,538,540,541,542,543,544,545,546,547,548,549,550,562,563,564,565,566,572,573,574,575,576,577,578,579,580,591,592,593,594,595,596,597,598,599,600,601,602,603,606,607,608,609,610,611,612,613,614,629,630,631,632,633,634,635,636,637,638,639,640,641,642,643,644,645,646,647,648,649,650,651,652,903,1237,1238,1239,1240,1241,1242,1243,1244,1245,1246,1247,1248,1249,1250,1251,1252,1253,1254,1255,1257,1258,1260,1261,1262,1263,1264,1275,1369,1661,1664,1665,1667,1685,1697,1698,1699,1700,1701,1702,1703,1704,1705,1706,1707,1708,1709,1710,1816,1817,1818,1819,1820,1821,1822,1823,1824,1825,1826,1827,1856,1896,2056,2057,2058,2181,2182,2183,2184,425,2214,2238,2241,2265,2266,2267,2268,2269,2270,2291,2290,2302,2301,2312,2313,662,406,320,2321,2322,2323,2324,2326,2327,2328,2329,2330,2331,2325,2332,2333,2334,2335,2337,2336,2370,2383,2384,2379,2377,2382,2381,2380,2386,2385,2387,2662,2663,2664,2665,2666,2789,2790,2903,2902,2905,2906,2907,2908,2909,2910,2919,2920,2922,2913,2914,2915,2916,2917,2918,2923,2924,2925,2926,2927,2928,2929,2933,2932,2925,2935,2934,2936,2937,2954,2953,2952,2951,2950,2955,2956,2957,3002,3009,3010,3011,3012,3013,3018,3017,3016,3023,3024,3025,3026,3027,3028,3029,3030,3032,3033,3042,3044,3047,3017,3031,3055,3059,3060,3062,3064,3065,3063,3067,3066,3068,3069,3070,3071,3076,3086,3087,3088,3089,3090,3091,3092,3093,1667,3112,3111,3113,2311,3132,3133,3156,3458,3459,3460,3461,3462,3464,3463,3465,3467,3474,3468,3469,3470,3471,3472,3473,3477,3479,3480,3481,3486,3487,3488,3489,3494,3497,3498,1246,3478,3507,3520,3564,3568,3575,3577,3576,3578,3579,3584,3698)
AND ((tl.loadingStarted IS NULL AND tl.loadingArrivalTime >= "2015-05-31 22:00:00") OR (tl.loadingStarted IS NOT NULL AND tl.loadingStarted >= "2015-05-31 22:00:00"))
GROUP BY ro.id
ORDER BY lf.name ASC;
