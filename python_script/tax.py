import sys         # `import sys` | Access system functions 

salary = int(sys.argv[1])  # `sys.argv` | Get command line arguments 

tax = salary * 0.05

print(int(tax))
