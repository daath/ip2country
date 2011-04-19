# Needs py2exe - run python makewinexe.py py2exe
from distutils.core import setup
import py2exe
      
setup(console=["makedb.py"])
