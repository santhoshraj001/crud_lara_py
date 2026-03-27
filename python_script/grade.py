import sys         # `import sys` | Access system functions 

salary = int(sys.argv[1])  # `sys.argv` | Get command line arguments 

if salary >= 100000:
    print("A")
elif salary >= 50000:
    print("B")
elif salary >= 10000:
    print("C")
else:
    print("D")