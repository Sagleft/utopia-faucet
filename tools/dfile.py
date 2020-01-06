#!/usr/bin/python3
import sys
import urllib.request

arg_url = sys.argv[1]
arg_file = sys.argv[2]

# headers = {}
# headers['User-Agent'] = "Mozilla/5.0 (X11; Ubuntu; Linux i686; rv:48.0) Gecko/20100101 Firefox/48.0"

# req = urllib.request.Request(arg_url, headers = headers)
# result = urllib.request.urlopen(req).read()
# print(result)

logo = urllib.request.urlopen(arg_url).read()
f = open(arg_file, "wb")
f.write(logo)
f.close()
